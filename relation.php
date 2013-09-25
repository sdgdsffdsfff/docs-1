<?php
include_once ("./header.php");
include_once ("./resource_helper.php");
include_once ("./messages.php");
include_once ("./functions.php");
require_once('appvars.php');

function build_query($docs, $count_only = false) {

    if ($count_only) {
        $search_query = "SELECT count(*) as count FROM resource";
    } else {
        $search_query = "SELECT * FROM resource";
    }

    $final_docs = explode('|', $docs);

    $first = true;
    foreach ($final_docs as $final_doc) {
        if ($final_doc != '') {
            if ($first) {
                $search_query .= " WHERE id=" . $final_doc;
                $first = false;
            } else {
                $search_query .= " OR id=" . $final_doc;
            }
        }
    }



    $search_query .= " ORDER BY title";

    //echo $search_query;
    return $search_query;
}

function render_content($row) {
    $title = $row[title];
    $id = $row[id];
    $def = $row[description];
    echo "<p><a href=\"resource_viewer.php?id=$id\">$title</a></p>";

    echo tcmks_substr($def);

    echo "<hr>";
}

function render_entity($dbc, $keywords) {
    $query = "SELECT * FROM def where name = '$keywords'";
    $result = mysqli_query($dbc, $query) or die('Error querying database.');
    if ($row = mysqli_fetch_array($result)) {

        render_content($row);
    }
}

// This function builds navigational page links based on the current page and the number of pages
function generate_page_links($id, $cur_page, $num_pages) {
    $page_links = '';

    echo '<ul class="pagination">';

    echo '<li><a href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&page=' . (1) . '">首页</a></li>';

    // If this page is not the first page, generate the "previous" link
    if ($cur_page > 1) {
        $page_links .= '<li><a href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&page=' . ($cur_page - 1) . '">上一页</a></li>';
    } else {
        $page_links .= '<li class="disabled"><a>上一页</a></li> ';
    }

    $start = 1;
    $end = $num_pages;

    if ($num_pages > 10) {

        if ($cur_page <= 5) {
            $start = 1;
            $end = 10;
        } elseif ($num_pages - $cur_page < 4) {
            $start = $num_pages - 9;
            $end = $num_pages;
        } else {
            $start = $cur_page - 5;
            $end = $cur_page + 4;
        }
    }


    // Loop through the pages generating the page number links
    for ($i = $start; $i <= $end; $i++) {
        if ($cur_page == $i) {
            $page_links .= ' <li class="active"><a>' . $i . '</a></li>';
        } else {
            $page_links .= ' <li><a href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&page=' . $i . '"> ' . $i . '</a></li>';
        }
    }

    // If this page is not the last page, generate the "next" link
    if ($cur_page < $num_pages) {
        $page_links .= ' <li><a href="' . $_SERVER['PHP_SELF'] . '?keywords=' . $user_search . '&sort=' . $sort . '&page=' . ($cur_page + 1) . '">下一页</a></li>';
    } else {
        $page_links .= ' <li class="disabled"><a>下一页</a></li>';
    }

    echo $page_links;
    echo '<li><a href="' . $_SERVER['PHP_SELF'] . '?keywords=' . $user_search . '&sort=' . $sort . '&page=' . ($num_pages) . '">尾页</a></li>';

    echo '</ul>';
}

function get_total($docs, $dbc) {
    $query = build_query($docs, true);
    $result = mysqli_query($dbc, $query);
    $row = mysqli_fetch_array($result);
    $total = $row['count'];
    return $total;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    render_warning('无相关实体信息');
}
?>
<div class="container">



    <?php
    $query = "SELECT * FROM relation where id ='$id'";
    $data = mysqli_query($dbc, $query);


    if ($row = mysqli_fetch_array($data)) {
        $docs = $row['DOCS'];
        $subject = $row['SUBJECT'];
        $predicate = $row['PREDICATE'];
        $object = $row['OBJECT'];
        $value = $row['VALUE'];
        $distance = $row['DISTANCE'];
        $frequency = $row['FREQUENCY'];
    }
    ?>
    <nav class="navbar navbar-default" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">潜在语义关系:&nbsp;<?php echo $subject . '&nbsp;-&nbsp;' . $object; ?></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav">
                <li><a class href="basic.php?action=create&type=期刊文献"><span class="glyphicon glyphicon-list"></span>&nbsp;录入TCMLS</a></li>               
                <li><a href="upload.php"><span class="glyphicon glyphicon-cloud-download"></span>&nbsp;下载RDF文件</a></li>               
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li><a href="#" >返回首页</a></li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </nav>
    <?php
    echo '<div class = "panel panel-default">';
    echo '<div class = "panel-heading">';
    echo '<strong>基本信息</strong>';
    echo '</div>';
    echo '<div class = "panel-body">';
    echo '<div class = "row">';

    echo '<div class = "col-md-1"><strong>主体:</strong></div>';
    echo '<div class = "col-md-11">' . $subject . '</div>';

    echo '<div class = "col-md-1"><strong>谓词:</strong></div>';
    echo '<div class = "col-md-11">' . $predicate . '</div>';

    echo '<div class = "col-md-1"><strong>客体:</strong></div>';
    echo '<div class = "col-md-11">' . $object . '</div>';

    echo '<div class = "col-md-1"><strong>赋值:</strong></div>';
    echo '<div class = "col-md-11">' . $value . '</div>';

    echo '<div class = "col-md-1"><strong>距离:</strong></div>';
    echo '<div class = "col-md-11">' . $distance . '</div>';

    echo '<div class = "col-md-1"><strong>频数:</strong></div>';
    echo '<div class = "col-md-11">' . $frequency . '</div>';

    echo '</div>';

    echo '</div>';
    echo '</div>';
    ?>
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#docs" data-toggle="tab">文献来源</a></li>
            <li><a href="#baidu" data-toggle="tab">百度搜索</a></li>   
            <li><a href="#tcmls" data-toggle="tab">TCMLS</a></li>   

            
        </ul>


        <div class="tab-content">



            <div class="tab-pane fade in active" id="docs">
                <?php
//$query = "SELECT * FROM resource where title like '%$keywords%' or description like '%$keywords%' ORDER BY title ASC LIMIT 0,10";
// Calculate pagination information
                $cur_page = isset($_GET['page']) ? $_GET['page'] : 1;
                $results_per_page = 10;  // number of results per page
                $skip = (($cur_page - 1) * $results_per_page);
                $total = get_total($docs, $dbc);
                $num_pages = ceil($total / $results_per_page);

                echo '<p></p>';

                echo '<p><font color="gray">出现于如下' . $total . '篇文献之中:</font></p>';
                echo '<hr>';
                $query = build_query($docs) . " LIMIT $skip, $results_per_page";


                $result = mysqli_query($dbc, $query) or die('Error querying database.');
                while ($row = mysqli_fetch_array($result)) {
                    render_content($row);
                }

                if ($num_pages > 1) {
                    generate_page_links($id, $cur_page, $num_pages);
                }
                ?>
            </div>
            <div class="tab-pane fade" id="baidu">
                <br>百度搜索，未完成...
            </div>

            <div class="tab-pane fade" id="tcmls">
                <br>语言系统，未完成...
            </div>

        </div>
    </div>












</div>



<!-- Example row of columns -->


<!-- /container -->
<?php
include_once ("./foot.php");
?>
