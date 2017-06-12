<!DOCTYPE html>
<html lang="kr">

<head>
    <?php
        require_once("./class/DBManager.php");
        if(!isset($_COOKIE['userEmail']))
        {
            header('Location: http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/index.php');
        }

        $db_manager = new DB_Manager();
        $stmt = $db_manager->pdo->prepare("SELECT user_num,email,name FROM User WHERE email = ?");
        $stmt->execute(array($_COOKIE['userEmail']));
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        //$user["name"] is user's name
        //$user["email"] is user's email
        //$stmt->rowCount(); result row count
    ?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>M Drive</title>
    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/main_page.css" rel="stylesheet">
    <link rel='stylesheet prefetch' href='http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css'>

    <!-- jQuery -->
    <script src="js/jquery.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
 
<body>
    <div id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <div class="user-head">
                    <a class="inbox-avatar" href="javascript:;">
                        <img width="60" hieght="60" src="https://openclipart.org/download/247319/abstract-user-flat-3.svg">
                    </a>
                    <div class="user-name">
                        <h5><a href="#"><?php echo $user["name"]?></a></h5>
                        <span><a href="#"><?php echo $user["email"]?></a></span>
                    </div>
                </div>
                <div class="inbox-body">
                    <a href="upload_movie.html" data-target="#uploadForm" data-toggle="modal" title="Compose" class="btn btn-compose">
                              UPLOAD
                    </a>
                </div>
                <ul class="inbox-nav inbox-divider">
                    <li class="active">
                        <a href="#"><i class="fa fa-inbox"></i> Inbox</a>
                        <!-- <span class="label label-danger pull-right"></span></a> -->
                    </li>
                    <li>
                        <a href="config.html" data-target="#configForm" data-toggle="modal" title="Compose"><i class=" fa fa-wrench"></i> Config</a>
                        <!-- <span class="label label-info pull-right">30</span></a> -->
                    </li>
                    <li>
                        <a href="./util/signout.php"><i class=" fa fa-trash-o"></i> Logout</a>
                    </li>
                </ul>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->
        <!-- Page Content -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-12">
                                <a href="#menu-toggle" class="btn btn-default" id="menu-toggle">Toggle Menu</a>
                                <h1 class="page-header"> 나의 파일들<small> 교수님 A길만 걷게 해주세요</small></h1>
                            </div>
                        </div>
                        <!-- Projects Row -->
                        <?php
                            $page = 1;
                            if(isset($_GET['page']))
                                $page = $_GET['page'];
                                if($page < 1)
                                    $page = 1;
                            $stmt = $db_manager->pdo->prepare("SELECT video_num,name,up_date FROM Video WHERE owner_num = ? ORDER BY up_date DESC");
                            $stmt->execute(array($user["user_num"]));
                            //code
                            $count = $stmt->rowCount();
                            $videos = $stmt->fetchall();
                            for($i = 0; ($page-1)*9 + ($i*3) < $count && $i < 3; $i++)
                            {
                                echo "<div class='row'>";
                                for($j = 0; $j+($i*3)+($page-1)*9 < $count && $j < 3; $j++)
                                {
                                    $addr = './streamingpage.php?num='.strval($videos[($page-1)*9+($i*3)+$j]["video_num"]);
                                    echo "<div class='col-md-4 portfolio-item'>";
                                    echo "<a href='".$addr."'>";
                                    echo "<img class='img-responsive' src='./file/thumbnail/".$user["email"]."/".$videos[($page-1)*9+($i*3)+$j]["name"].".png'  height='400'
                                    onerror=\"this.src='http://placehold.it/700x400'\">";
                                    //echo "<img class='img-responsive' src='http://placehold.it/700x400' alt=''>";
                                    //echo "<video width='700' height='400'>";
                                    //echo "<source src='./file/video/".$_COOKIE['userEmail']."/".$videos[($page-1)*9+($i*3)+$j]["name"]."' type='video/mp4'>";
                                    //echo "</video>";
                                    echo "</a>";
                                    echo "<h3><a href='".$addr."'>".$videos[($page-1)*9+($i*3)+$j]["name"]."</a></h3>";
                                    echo "<p>Upload Date : ".$videos[($page-1)*9+($i*3)+$j]["up_date"]."</p>";
                                    echo "</div>";
                                }
                                echo "</div>";
                            }
                        ?>
                        <!-- /.row -->
                        <hr>
                        <!-- Pagination -->
                        <div class="row text-center">
                            <div class="col-lg-12">
                                <ul class="pagination">
                                    <?php
                                        //it is not necessary. just better viewing
                                        $page = 1;
                                        if(isset($_GET['page']))
                                            $page = $_GET['page'];
                                            if($page < 1)
                                                $page = 1;
                                        $max = intval(($count-1)/9)+1; //0~9 is page1 10~18 page2
                                        if($page <= 1)
                                           echo "<li><a href='#'>&laquo;</a></li>";
                                        else
                                           echo "<li><a href='./mainpage.php?page=".strval($page-1)."'>&laquo;</a></li>";

                                        for($i = $page-4,$c = 0; $c < 9; ++$i,++$c)
                                        {
                                            if($i < 1 || $i > $max)
                                                continue;
                                            if($i == $page)
                                                echo "<li class='active'><a href='#'>".$i."</a></li>";
                                            else
                                                echo "<li><a href='./mainpage.php?page=".$i."'>".$i."</a></li>";
                                        }

                                        if($page >= $max)
                                            echo "<li><a href='#'>&raquo;</a></li>";
                                        else
                                            echo "<li><a href='./mainpage.php?page=".strval($page+1)."'>&raquo;</a></li>";
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                </div>
            </div>
            <!-- /#page-content-wrapper -->
        </div>
        <!-- /#wrapper -->
        <!-- Menu Toggle Script -->
        <script>
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });
        </script>

        <div class="modal fade" id="uploadForm" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                </div>
            </div>
        </div>
        <div class="modal fade" id="configForm" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                </div>
            </div>
        </div>
</body>

</html>
