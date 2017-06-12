<!DOCTYPE html>
<html lang="kr">

<head>
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
    <style type="text/css">
    <?php
        require_once("./class/DBManager.php");

        // 유저 이메일 쿠키가 존재하는 지 확인
        // 없다면 초기 페이지로...
        if(!isset($_COOKIE['userEmail']))
            header('Location: http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/index.php');
        // 비디오 넘버를 얻습니다.
        // 비디오 넘버를 얻지 못했다면 메인 페이지로...
        if(!isset($_GET['num']))
            header('Location: http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/mainpage.php');

        /*
            유저 이메일 쿠키로 유저 정보를 DB에 요청합니다.
        */
        $db_manager = new DB_Manager();
        $stmt = $db_manager->pdo->prepare("SELECT user_num FROM User WHERE email = ?");
        $stmt->execute(array($_COOKIE['userEmail']));
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        /*
            유저 정보로 비디오 정보를 DB에 요청합니다.
        */
        $stmt = $db_manager->pdo->prepare("SELECT name,caption_num FROM Video WHERE owner_num = ? and video_num = ?");
        $stmt->execute(array($user["user_num"],$_GET["num"]));
        if($stmt->rowCount() != 1) // 비디오가 존재하지 않다면 메인 페이지로...
            header('Location: http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/mainpage.php');
        $video = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>

    body {
        background-color: #000000;
        margin: 0px;
        /*hide scroll*/
    }
    
    video::-webkit-media-controls-fullscreen-button {
        display: none;
    }
    </style>
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
                <div class="user-head">>
                    <div class="user-name">
                        <h5><a href="#"><?php echo $video['name']; ?></a></h5>
                        <span><a href="#">이 동영상을 위한 자막</a></span>
                    </div>
                </div>
                <div class="inbox-body">
                    <a href="upload_subtitle.html" data-target="#uploadForm" data-toggle="modal" title="Compose" class="btn btn-compose">
                              UPLOAD
                    </a>
                </div>
                <ul class="inbox-nav inbox-divider">
                    <li class="active">
                        <!-- 영상에 링킹되있는 자막들 출력 -->
                        <?php
                            /*
                                유저 정보와 비디오 넘버로 자막을 DB에 자막을 요청하고
                                자막 리스트를 보여줍니다.
                            */
                            $stmt = $db_manager->pdo->prepare("SELECT caption_num,name FROM Caption WHERE video_num = ? and owner_num = ?");
                            $stmt->execute(array($_GET['num'], $user['user_num']));
                            $count = $stmt->rowCount();
                            if($count == 0)
                            {
                                echo "<a href='#'><i class='fa fa-file-text'></i> no caption </a>";
                            }
                            else
                            {
                                for($i = 0; $i < $count; ++$i)
                                {
                                    $caption = $stmt->fetch(PDO::FETCH_ASSOC);
                                    echo "<a href='#'><i class='fa fa-file-text'></i>".$caption['name']."</a>";
                                }
                            }
                        ?>
                    </li>
                </ul>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->
        <div id="page-content-wrapper">
            <div class="row">
                <a>
                    <image id="subtitle-toggle" href="#subtitle-toggle" src="img/subtitle-btn.png "></image>
                    <image id="back-toggle" src="img/exit-btn.png" align="right" onClick="history.back();"></image>
                </a>
                <video id="vt" width="400" height="100" controls>
                    <?php
                        /*
                            비디오와 자막 리스트를 사용해
                            video 태그를 채웁니다.
                        */

                        echo "<source src='./file/video/".$_COOKIE['userEmail']."/".$video['name']."' type='video/mp4'>";
                        $stmt = $db_manager->pdo->prepare("SELECT caption_num,name FROM Caption WHERE video_num = ? and owner_num = ?");
                        $stmt->execute(array($_GET['num'], $user['user_num']));
                        $count = $stmt->rowCount();
                        for($i = 0; $i < $count; ++$i)
                        {
                            $caption = $stmt->fetch(PDO::FETCH_ASSOC);
                            echo "<track kind='subtitles' label='".$caption['name']."' src='./file/caption/".$_COOKIE['userEmail']."/".$caption['name']."' ";
                            if($caption['caption_num'] == $video['caption_num'])
                                echo "default";
                            echo ">";
                        }

                    ?>
                    what the?
                </video>
                <!-- Projects Row -->
            </div>
        </div>
        <script>
        $("#subtitle-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });
        </script>
        <script type="text/javascript">
        $(document).ready(function() {
            $("#vt").attr({
                "width": $(window).width(),
                "height": $(window).height()
            });
        });
        $(window).resize(function() {
            $("#vt").attr({
                "width": $(window).width(),
                "height": $(window).height()
            });
        });
        </script>
        <div class="modal fade" id="uploadForm" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                </div>
            </div>
        </div>
    </div>
</body>

</html>
