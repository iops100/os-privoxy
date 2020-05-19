<?php
require_once("guiconfig.inc");
require_once("system.inc");
require_once("interfaces.inc");
require_once 'diag_logs_common.inc';

$logfile = '/var/log/privoxy/logfile';
$logclog = false;

$service_hook = 'privoxy';

//Date and type fields number
$logsplit = 3;

$filtertext = '';
$nentries = 50;

if (isset($config['syslog']['nentries'])) {
	$nentries = $config['syslog']['nentries'];
}

if (!empty($_POST['clear'])) {
	if ($logclog) {
		system_clear_clog($logfile);
	} else {
		system_clear_log($logfile);
	}
	if (function_exists('clear_hook')) {
		clear_hook();
	}
}

if (isset($_POST['filtertext'])) {
	$filtertext = $_POST['filtertext'];
}

include("head.inc");
?>
<body>
<?php 
include("fbegin.inc"); 
?>
  <section class="page-content-main">
    <div class="container-fluid">
      <div class="row">
<?php 
if(!empty($logfile)){
?>
    <div class="container-fluid">
      <div class="row">
        <section class="col-xs-12">
          <p>
            <form method="post">
              <div class="input-group">
                <div class="input-group-addon"><i class="fa fa-search"></i></div>
                <input type="text" class="form-control" id="filtertext" name="filtertext" placeholder="<?= html_safe(gettext('Search for a specific message...')) ?>" value="<?= html_safe($filtertext) ?>"/>
                <input type="hidden" name="list_log" id="list_log" value="<?= html_safe($logfile) ?>"/>
              </div>
            </form>
          </p>
          <div class="table-responsive content-box tab-content">
            <table class="table table-striped">
              <tr>
                <th class="col-md-2 col-sm-3 col-xs-4"><?= gettext('Date') ?></th>
                <th class="col-md-10 col-sm-9 col-xs-8"><?= gettext('Message') ?></th>
              </tr>
              <?php if (isset($logpills)): ?>
              <tr>
                <td colspan="2">
                  <ul class="nav nav-pills" role="tablist">
                    <?php foreach ($logpills as $pill): ?>
                    <li role="presentation" <?php if (str_replace('amp;','', $pill[2]) == $_SERVER['REQUEST_URI']):?>class="active"<?php endif; ?>><a href="<?=$pill[2];?>"><?=$pill[0];?></a></li>
                    <?php endforeach; ?>
                  </ul>
                </td>
              </tr>
              <?php endif; ?>
              <?php
                if ($logclog) {
                    dump_clog($logfile, $nentries, $filtertext);
                } else {
                    dump_log($logfile, $nentries, $filtertext);
                }
              ?>
              <tr>
                <td colspan="2">
                  <form method="post">
<?php                   if (isset($mode)): ?>
                    <input type="hidden" name="mode" id="mode" value="<?= html_safe($mode) ?>"/>
<?php                   endif; ?>
					<input type="hidden" name="list_log" id="list_log" value="<?= html_safe($logfile) ?>"/>
                    <input name="clear" type="submit" class="btn btn-primary" value="<?= html_safe(gettext('Clear log')) ?>"/>
                  </form>
                </td>
              </tr>
            </table>
          </div>
        </section>
      </div>
    </div>
<?php } ?>
  </section>
<?php include("foot.inc"); ?>
