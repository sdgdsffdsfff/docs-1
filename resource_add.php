<?php
include_once ("./header.php");
include_once ("./resource_helper.php");
include_once ("./functions.php");
require_once('appvars.php');
include_once ("./db_helper.php");
echo '<p></p>';

if (isset($_POST['submit'])) {

    $creator = $_POST['creator'];
    $publisher = $_POST['publisher'];
    $description = $_POST['description'];
    $identifier = $_POST['identifier'];
    $source = $_POST['source'];
    $type = isset($_POST['type']) ? $_POST['type'] : '其他资源';
    $subject = $_POST['subject'];

    if (isset($_POST['title']) && ($_POST['title'] != '')) {
        $title = $_POST['title'];
        $file_id = init_resource($dbc, $type);
        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            $file_name = upload_file($db_name, $file_id);
        }

        $query = "update resource set ";

        $query .= "title = '" . mysql_escape_string($title) . "',";

        if ('' != $file_name) {
            $query .= "file = '$file_name',";
        }

        $query .= "source='" . mysql_escape_string($source) . "',";
        $query .= "creator='" . mysql_escape_string($creator) . "',";
        //$query .= "description = '".tcmks_substr(mysql_escape_string($description),1000)."',";
        $query .= "description = '" . mysql_escape_string($description) . "',";
        $query .= "publisher = '" . mysql_escape_string($publisher) . "', ";
        $query .= "identifier = '" . mysql_escape_string($identifier) . "', ";
        $query .= "subject = '" . mysql_escape_string($subject) . "' ";

        $query .= " where id = '$file_id'";

        //echo $query;
        //$query = "INSERT INTO resource VALUES ('$file_id', '$title', '$file_name', '$creator', '$journal', '$pages', '$year', '$publisher',NULL)";
        mysqli_query($dbc, $query);

        echo '<div class="alert alert-success">';
        echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
        echo '<h4>文献录入成功!</h4>';
        echo '文献信息如下：';
        echo '<dl class="dl-horizontal">';
        echo "<dt>文献题目:</dt><dd>" . $title . '</dd>';
        echo "<dt>文献类型:</dt><dd>" . $type . '</dd>';
        if ('' != $file_name) {
            echo "<dt>文件名称:</dt><dd>" . $file_name . "</dd>";
            echo "<dt>文件类型:</dt><dd>" . $_FILES["file"]["type"] . "</dd>";
            echo "<dt>文件尺寸:<dt><dd>" . ($_FILES["file"]["size"] / 1024) . "Kb</dd>";
        } else {
            echo '您没有上传原文！';
        }
        echo '</dl></div>';
    } else {
        echo '<div class="alert alert-success">';
        echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
        echo '<h4>文献录入失败!</h4>';
        echo '您没有填写文献标题！';
        echo '</div>';
    }
}
//echo "Stored in: " . "upload/" . $_FILES["file"]["name"];
?>

<div class="container">

    <?php include_once ('resource_header.php'); ?>


    <form role="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="form-horizontal"
          enctype="multipart/form-data">
        <legend>请录入文献的基本信息：</legend>
        <input  type="hidden" id="db_name" name="db_name" value = "<?php if (isset($db_name)) echo $db_name; ?>" >

        <div class="form-group">
            <label class="col-sm-2 control-label" for="title">题名:</label>
            <div class="col-sm-10">
                <input class="form-control" type="text" id="title" name="title" value = "<?php if (isset($title)) echo $title; ?>" placeholder="请输入文献的题目">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="identifier">标识:</label>
            <div class="col-sm-10">
                <input class="form-control" type="text" id="identifier" name="identifier" value = "<?php if (isset($identifier)) echo $identifier; ?>" placeholder="请输入文献的标识">
            </div>
        </div>



        <div class="form-group">
            <label class="col-sm-2 control-label" for="creator">创建者:</label>
            <div class="col-sm-10">
                <input class="form-control" type="text" id="creator" name="creator" value = "<?php if (isset($creator)) echo $creator; ?>" placeholder="请输入作者">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="title">类型:</label>
            <div class="col-sm-10">
                <input class="form-control" type="text" id="type" name="type" value = "<?php if (isset($type)) echo $type; ?>" placeholder="请输入文献的类型">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="source">出处:</label>
            <div class="col-sm-10">
                <input class="form-control" type="text" id="source" name="source" value = "<?php if (isset($source)) echo $source; ?>" placeholder="请输入出处">

            </div>
        </div>               

        <div class="form-group">
            <label class="col-sm-2 control-label" for="publisher">出版者:</label>
            <div class="col-sm-10">
                <input class="form-control" type="text" id="publisher" name="publisher" value = "<?php if (isset($publisher)) echo $publisher; ?>" placeholder="请输入出版者">

            </div>
        </div>               

        <div class="form-group">
            <label class="col-sm-2 control-label" for="subject">主题:</label>
            <div class="col-sm-10">
                <input class="form-control" type="text" id="subject" name="subject" value = "<?php if (isset($subject)) echo $subject; ?>" placeholder="请输入主题">

            </div>
        </div>               


        <div class="form-group">
            <label class="col-sm-2 control-label" for="description">描述:</label>
            <div class="col-sm-10">
                <textarea class="form-control" id="description" name="description"  placeholder="请输入描述" rows="6"><?php if (isset($description)) echo $description; ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="file">上传原文:</label>
            <div class="col-sm-10">
                <input class="form-control" type="file" name="file" id="file" />                
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <input class="btn btn-primary" type="submit" name="submit" value="&nbsp;&nbsp;提&nbsp;交&nbsp;&nbsp;" />   
               
            </div>
        </div>

    </form>


</div>

<?php
include_once ("./foot.php");
?>
