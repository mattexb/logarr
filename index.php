<?php
include('assets/php/functions.php');
include('assets/php/auth_check.php');
?>

<!DOCTYPE html>
<html lang="en">

<!--
				LOGARR
	by @seanvree, @jonfinley, and @rob1998
		https://github.com/Monitorr
-->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link rel="manifest" href="webmanifest.json">

    <meta name="Logarr" content="Logarr: Self-hosted, single-page, log consolidation tool." />
    <meta name="description" content="Logarr">
    <meta name="application-name" content="Logarr" />
    <meta name="robots" content="NOINDEX, NOFOLLOW">

    <script src="assets/js/pace.js" async></script>

    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="180x180" href="assets/images/favicon/apple-touch-icon.png">
    <link rel="mask-icon" href="assets/images/favicon/icon.svg" color="blue">

    <link rel="icon" type="image/png" href="assets/images/favicon/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="assets/images/favicon/favicon-16x16.png" sizes="16x16">
    <meta name="msapplication-square150x150logo" content="assets/images/favicon/mstile-150x150.png">

    <meta name="theme-color" content="#464646" />
    <meta name="theme_color" content="#464646" />
    <meta name="msapplication-TileColor" content="#464646" />

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/vendor/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/logarr.css">
    <link rel="stylesheet" href="assets/data/custom.css">

    <title>
        <?php
        $title = $GLOBALS['preferences']['sitetitle'];
        echo $title . PHP_EOL;
        ?>
    </title>

    <style>
        body {
            margin-bottom: 3rem;
        }

        .swal2-icon.swal2-warning {
            color: yellow;
            border-color: yellow;
        }
    </style>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/vendor/sweetalert2.min.js"></script>
    <script src="assets/js/jquery.highlight.js" async></script>
    <script src="assets/js/jquery.mark.min.js" async></script>
    <script src="assets/js/logarr.main.js"></script>

    <?php appendLog($logentry = "Logarr index loaded"); ?>

    <!-- Check if Logarr auth is enabled / if TRUE, check login status every 10s -->
    <?php checkLoginindex(); ?>

    <!-- sync config with javascript -->
    <script>
        let settings = <?php echo json_encode($GLOBALS['settings']); ?>;
        let preferences = <?php echo json_encode($GLOBALS['preferences']); ?>;
        let logs = <?php echo json_encode($GLOBALS['logs']); ?>;
        let authentication = <?php echo json_encode($GLOBALS['authentication']); ?>;
        home = true;
        refreshConfig();
    </script>

    <!-- UI clock functions: -->
    <script>
        <?php
        //initial values for clock:
        //$timezone = $GLOBALS['preferences']['timezone'];
        $dt = new DateTime("now", new DateTimeZone("$timezone"));
        $timeStandard = (int)($GLOBALS['preferences']['timestandard']);
        $rftime = $GLOBALS['settings']['rftime'];
        $timezone_suffix = '';
        if (!$timeStandard) {
            $dateTime = new DateTime();
            $dateTime->setTimeZone(new DateTimeZone($timezone));
            $timezone_suffix = $dateTime->format('T');
            //TODO:
            // appendLog(
            // 	$logentry = "ERROR: Could not load time standard value"
            // );
        }
        $serverTime = $dt->format("D d M Y H:i:s");
        ?>
        let servertime = "<?php echo $serverTime; ?>";
        let timeStandard = <?php echo $timeStandard; ?>;
        let timeZone = "<?php echo $timezone_suffix; ?>";
        let rftime = <?php echo $GLOBALS['settings']['rftime']; ?>;

        $(document).ready(function() {
            syncServerTime()
            setInterval(function() {
                syncServerTime()
            }, settings.rftime); //delay is rftime
            updateTime();
        });
    </script>

    <script src="assets/js/clock.js" async></script>
    <script src="assets/data/custom.js"></script>

    <!-- Append settings values to Logarr log: -->
    <?php settingsValues(); ?>

</head>

<body id="body" onscroll="scrollFunction()" onload="refreshblockUI();">

    <script>
        document.body.className += ' fade-out';
        $(function() {
            $('body').removeClass('fade-out');
        });
    </script>

    <div class="header">

        <div id="left" class="Column">

            <div id="clock">
                <i class="fas fa-exclamation-triangle hidden" id="synctimeerror" title="An error occurred while synchronizing time!"> </i>
                <canvas id="canvas" width="120" height="120"></canvas>
                <div class="dtg" id="timer"></div>
            </div>

        </div>

        <div id="logo" class="Column">
            <img id="logo-icon" src="assets/images/logo_white_glow_text_logarr-crop.png" alt="Logarr">
            <div id="brand" class="header-brand" title="Reload Logarr" onclick="window.location.reload(true);">
                <?php
                echo $preferences['sitetitle'];
                ?>
            </div>
        </div>

        <div id="right" class="Column">

            <div id="righttop" class="righttop">
                <form method="POST">
                    <div id="markform">

                        <input type="search" name="markinput" id="text-search2" class="input" title="Input search query" placeholder=" Search & highlight . . ." required spellcheck="false">
                        <button data-search="search" name="searchBtn" id="searchBtn" value="Search" class="btn marksearch btn-primary" onclick="this.blur(); return false;" title="Execute search. Results will be highlighted in yellow.">Search
                        </button>
                        <button data-search="next" name="nextBtn" class="btn search-button btn-primary btn-visible btn-hidden" onclick="this.blur(); return false;" title="Focus to first search result">&darr;
                        </button>
                        <button data-search="prev" name="prevBtn" class="btn search-button btn-primary btn-visible btn-hidden" onclick="this.blur(); return false;" title="Focus to last search result">&uarr;
                        </button>
                        <button data-search="clear" class="btn search-button btn-primary" onclick="this.blur(); return false;" title="Clear search results">✖
                        </button>

                    </div>
                </form>
            </div>

            <div id="rightmiddle" class="rightmiddle">

                <div id="count" class="count" title="Search results have been highlighted in yellow."></div>

            </div>

            <div id="rightbottom" class="rightbottom">

                <div id="auto-update" title="Auto-update logs | Interval: <?php echo $settings['rflog']; ?> ms ">

                    Auto Update:

                    <label class="switch" id="buttonStart">
                        <span class="slider round" id="autoUpdateSlider" data-enabled="false" onclick="overwriteLogUpdate();"></span>
                    </label>

                    <input id="Update" type="button" name="updateBtn" class="button2 btn btn-primary" value="Update" title="Trigger log manual update" onclick="refreshblockUI(); this.blur(); return false" />
                </div>

            </div>

        </div>

    </div>

    <div id="logcontainer">
        <nav id="categoryFilter" style="display: none;"></nav>
        <div id='logwrapper' class='flex'></div>
    </div>

    <button onclick="topFunction();checkAll1();" id="myBtn" title="Go to top"></button>

    <div id="footer">

        <!-- Checks for Logarr application update on page load-->
        <script src="assets/js/update.js" async></script>

        <div id="logarrid">
            <a href="https://github.com/monitorr/logarr" title="Logarr GitHub repo" target="_blank" class="footer">Logarr </a> |
            <a href="https://github.com/Monitorr/logarr/releases" title="Logarr releases" target="_blank" class="footer">
                v:
                <?php echo file_get_contents("assets/js/version/version.txt"); ?></a> |
            <a href="settings.php" title="Logarr Settings" target="_blank" class="footer">Settings</a>
            <?php if (isset($_SESSION['user_name']) && isset($_SESSION['user_is_logged_in']) && !empty($_SESSION['user_name']) && ($_SESSION['user_is_logged_in'])) {
                echo " | <a href='index.php?action=logout' onclick='logouttoast();'  title='Log out' class='footer'></i>Logout</a>";
            } ?>
            <br>
        </div>

    </div>

</body>

</html> 