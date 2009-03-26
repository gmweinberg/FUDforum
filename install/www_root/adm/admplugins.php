<?php
/**
* copyright            : (C) 2001-2009 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: admplugins.php,v 1.1 2009/03/26 17:24:27 frank Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; version 2 of the License.
**/

	require('./GLOBALS.php');
	fud_use('adm.inc', true);
	fud_use('glob.inc', true);
	fud_use('widgets.inc', true);
	fud_use('plugins.inc', true);
	fud_use('draw_select_opt.inc');

	$help_ar = read_help();

	if (isset($_POST['form_posted'])) {
		foreach ($_POST as $k => $v) {
			if (!strncmp($k, 'FUD_OPT_3', 9)) {
				$NEW_FUD_OPT_3 = (int) $v;
			}
		}

		if (($NEW_FUD_OPT_3 ^ $FUD_OPT_3) & (4194304)) {
			if (!($NEW_FUD_OPT_3 & 4194304)) {
				$FUD_OPT_3 &= ~4194304;
			} else {
				$FUD_OPT_3 |= 4194304;
			}
			change_global_settings(array('FUD_OPT_3' => $FUD_OPT_3));
			$GLOBALS['FUD_OPT_3'] = $FUD_OPT_3;
		}
	}

	$prev_plugins = plugin_load_from_cache();
	if (isset($_POST['plugin_state'])) {
		plugin_rebuild_cache($_POST['plugins']);
	}
	$plugins = plugin_load_from_cache();

	while (list($key, $val) = @each($plugins)) {
		if (! in_array($val, $prev_plugins)) {
			echo "Install/enable plugin: ". $val ."<br />\n";
		}
	}
	while (list($key, $val) = @each($prev_plugins)) {
		if (! in_array($val, $plugins)) {
			echo "Deinstall/disable plugin: ". $val ."<br />\n";
		}
	}

	require($WWW_ROOT_DISK . 'adm/admpanel.php');
?>
<h2>Plugin Manager</h2>
<form method="post" action="admplugins.php" autocomplete="off">
<?php echo _hs ?>
<table class="datatable solidtable">
<?php
	print_bit_field('Plugins Enabled', 'PLUGINS_ENABLED');
?>
<tr class="fieldaction"><td colspan="2" align="left"><input type="submit" name="btn_submit" value="Set" /></td></tr>
</table>
<input type="hidden" name="form_posted" value="1" />
</form>

<h2>Available plugins:</h2>
<form method="post" action="admplugins.php" autocomplete="off">
<?php echo _hs ?>
<table class="datatable solidtable">
<tr class="fieldtopic"><td><b>Plugin name</b></td><td><b>Plugin enabled?</b></td></tr>
<?php
$disabled = ($GLOBALS['FUD_OPT_3'] & 4194304) ? '' : 'disabled="disabled"';
foreach (glob("$PLUGIN_PATH/*.plugin") as $plugin_path) {
	$plugin = basename($plugin_path);
	$checked = in_array($plugin, $plugins) ? 'checked="checked" ' : '';
?>
<tr class="field center">
  <td><?php echo $plugin; ?></td>
  <td><input type="checkbox" name="plugins[]" value="<?php echo $plugin; ?>" <?php echo $checked; $disabled; ?> /></td>
</tr>
<?php } ?>
<tr class="fieldtopic center">
  <td>&nbsp;</td>
  <td><input type="submit" name="plugin_state" value="Change state" <?php echo $disabled; ?> /></td>
</table>
</form>

<br />
<table class="tutor" width="99%"><tr><td>
Plugins are stored in: <?php echo $PLUGIN_PATH; ?><br />
To add new plugins, upload them to this directory and enable them on this page.
</td></tr></table>

<?php require($WWW_ROOT_DISK . 'adm/admclose.html'); ?>
