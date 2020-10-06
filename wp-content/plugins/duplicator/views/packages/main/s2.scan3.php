<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
	/*IDE Helper*/
	/* @var $Package DUP_Package */
	function _duplicatorGetRootPath() {
		$txt   = __('Root Path', 'duplicator');
		$root  = duplicator_get_abs_path();
		$sroot = strlen($root) > 50 ? substr($root, 0, 50) . '...' : $root;
		echo "<div title='{$root}' class='divider'><i class='fa fa-folder-open'></i> {$sroot}</div>";
	}

$archive_type_label		=  DUP_Settings::Get('archive_build_mode') == DUP_Archive_Build_Mode::ZipArchive ? "ZipArchive" : "DupArchive";
$archive_type_extension =  DUP_Settings::Get('archive_build_mode') == DUP_Archive_Build_Mode::ZipArchive ? "zip" : "daf";
$duparchive_max_limit   = DUP_Util::readableByteSize(DUPLICATOR_MAX_DUPARCHIVE_SIZE);
$skip_archive_scan    = DUP_Settings::Get('skip_archive_scan');
?>

<!-- ================================================================
ARCHIVE -->
<div class="details-title">
	<i class="far fa-file-archive"></i>&nbsp;<?php esc_html_e('Archive', 'duplicator');?>
	<sup class="dup-small-ext-type"><?php echo esc_html($archive_type_extension); ?></sup>
	<div class="dup-more-details" onclick="Duplicator.Pack.showDetailsDlg()" title="<?php esc_attr_e('Show Scan Details', 'duplicator');?>"><i class="fa fa-window-maximize"></i></div>
</div>

<div class="scan-header scan-item-first">
	<i class="far fa-copy fa-sm"></i>
	<?php esc_html_e("Files", 'duplicator'); ?>
	
	<div class="scan-header-details">
		<div class="dup-scan-filter-status">
			<?php
				if ($Package->Archive->ExportOnlyDB) {
					echo '<i class="fa fa-filter fa-sm"></i> ';
					esc_html_e('Database Only', 'duplicator');
				} elseif ($Package->Archive->FilterOn) {
					echo '<i class="fa fa-filter fa-sm"></i> ';
					esc_html_e('Enabled', 'duplicator');
				}
			?>
		</div>
		<div id="data-arc-size1"></div>
		<i class="fa fa-question-circle data-size-help"
			data-tooltip-title="<?php esc_attr_e('Archive Size', 'duplicator'); ?>"
			data-tooltip="<?php esc_attr_e('This size includes only files BEFORE compression is applied. It does not include the size of the '
						. 'database script or any applied filters.  Once complete the package size will be smaller than this number.', 'duplicator'); ?>"></i>

		<div class="dup-data-size-uncompressed"><?php esc_html_e("uncompressed"); ?></div>
	</div>
</div>

<?php
if ($Package->Archive->ExportOnlyDB) { ?>
<div class="scan-item ">
	<div class='title' onclick="Duplicator.Pack.toggleScanItem(this);">
		<div class="text"><i class="fa fa-caret-right"></i> <?php esc_html_e('Database only', 'duplicator');?></div>
		<div id="only-db-scan-status"><div class="badge badge-warn"><?php esc_html_e("Notice", 'duplicator'); ?></div></div>
	</div>
    <div class="info">
        <?php esc_html_e("Only the database and a copy of the installer.php will be included in the archive.zip file.", 'duplicator'); ?>
    </div>
</div>
<?php
} else if ($skip_archive_scan) { ?>
<div class="scan-item ">
	<div class='title' onclick="Duplicator.Pack.toggleScanItem(this);">
		<div class="text"><i class="fa fa-caret-right"></i> <?php esc_html_e('Skip archive scan enabled', 'duplicator');?></div>
		<div id="skip-archive-scan-status"><div class="badge badge-warn"><?php esc_html_e("Notice", 'duplicator'); ?></div></div>
	</div>
    <div class="info">
        <?php esc_html_e("All file checks are skipped. This could cause problems during extraction if problematic files are included.", 'duplicator'); ?>
        <br><br>
        <b><?php esc_html_e(" Disable the advanced option to re-enable file controls.", 'duplicator'); ?></b>
    </div>
</div>
<?php
} else {
?>

<!-- ============
TOTAL SIZE -->
<div class="scan-item">
	<div class="title" onclick="Duplicator.Pack.toggleScanItem(this);">
		<div class="text"><i class="fa fa-caret-right"></i> <?php esc_html_e('Size Checks', 'duplicator');?></div>
		<div id="data-arc-status-size"></div>
	</div>
	<div class="info" id="scan-itme-file-size">
		<b><?php esc_html_e('Size', 'duplicator');?>:</b> <span id="data-arc-size2"></span>  &nbsp; | &nbsp;
		<b><?php esc_html_e('File Count', 'duplicator');?>:</b> <span id="data-arc-files"></span>  &nbsp; | &nbsp;
		<b><?php esc_html_e('Directory Count', 'duplicator');?>:</b> <span id="data-arc-dirs"></span> <br/>
		<?php
			_e('Compressing larger sites on <i>some budget hosts</i> may cause timeouts.  ' , 'duplicator');
			echo "<i>&nbsp; <a href='javascipt:void(0)' onclick='jQuery(\"#size-more-details\").toggle(100);return false;'>[" . esc_html__('more details...', 'duplicator') . "]</a></i>";
		?>
		<div id="size-more-details">
			<?php
				echo "<b>" . esc_html__('Overview', 'duplicator') . ":</b><br/>";
				$dup_byte_size = '<b>' . DUP_Util::byteSize(DUPLICATOR_SCAN_SIZE_DEFAULT) . '</b>';
				printf(esc_html__('This notice is triggered at [%s] and can be ignored on most hosts.  If during the build process you see a "Host Build Interrupt" message then this '
					. 'host has strict processing limits.  Below are some options you can take to overcome constraints set up on this host.', 'duplicator'), $dup_byte_size);
				echo '<br/><br/>';

				echo "<b>" . esc_html__('Timeout Options', 'duplicator') . ":</b><br/>";
				echo '<ul>';
				echo '<li>' . esc_html__('Apply the "Quick Filters" below or click the back button to apply on previous page.', 'duplicator') . '</li>';
				echo '<li>' . esc_html__('See the FAQ link to adjust this hosts timeout limits: ', 'duplicator') . "&nbsp;<a href='https://snapcreek.com/duplicator/docs/faqs-tech/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_campaign=problem_resolution&utm_content=pkg_s2scan3_tolimits#faq-trouble-100-q' target='_blank'>" . esc_html__('What can I try for Timeout Issues?', 'duplicator') . '</a></li>';
				echo '<li>' . esc_html__('Consider trying multi-threaded support in ', 'duplicator');
				echo "<a href='https://snapcreek.com/duplicator/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_content=multithreaded_pro&utm_campaign=duplicator_pro' target='_blank'>" . esc_html__('Duplicator Pro.', 'duplicator') . "</a>";
				echo '</li>';
				echo '</ul>';

				$hlptxt = sprintf(__('Files over %1$s are listed below. Larger files such as movies or zipped content can cause timeout issues on some budget hosts.  If you are having '
				. 'issues creating a package try excluding the directory paths below or go back to Step 1 and add them.', 'duplicator'),
				DUP_Util::byteSize(DUPLICATOR_SCAN_WARNFILESIZE));
			?>
		</div>
		<script id="hb-files-large" type="text/x-handlebars-template">
			<div class="container">
				<div class="hdrs">
					<span style="font-weight:bold">
						<?php esc_html_e('Quick Filters', 'duplicator'); ?>
						<sup><i class="fas fa-question-circle fa-sm" data-tooltip-title="<?php esc_attr_e("Large Files", 'duplicator'); ?>" data-tooltip="<?php echo esc_attr($hlptxt); ?>"></i></sup>
					</span>
					<div class='hdrs-up-down'>
						<i class="fa fa-caret-up fa-lg dup-nav-toggle" onclick="Duplicator.Pack.toggleAllDirPath(this, 'hide')" title="<?php esc_attr_e("Hide All", 'duplicator'); ?>"></i>
						<i class="fa fa-caret-down fa-lg dup-nav-toggle" onclick="Duplicator.Pack.toggleAllDirPath(this, 'show')" title="<?php esc_attr_e("Show All", 'duplicator'); ?>"></i>
					</div>
				</div>
				<div class="data">
					<?php _duplicatorGetRootPath();	?>
					{{#if ARC.FilterInfo.Files.Size}}
						{{#each ARC.FilterInfo.TreeSize as |directory|}}
							<div class="directory">
								<i class="fa fa-caret-right fa-lg dup-nav" onclick="Duplicator.Pack.toggleDirPath(this)"></i> &nbsp;
								{{#if directory.iscore}}
									<i class="far fa-window-close chk-off" title="<?php esc_attr_e('Core WordPress directories should not be filtered. Use caution when excluding files.', 'duplicator'); ?>"></i>
								{{else}}
									<input type="checkbox" name="dir_paths[]" value="{{directory.dir}}" id="lf_dir_{{@index}}" onclick="Duplicator.Pack.filesOff(this)" />
								{{/if}}
								<label for="lf_dir_{{@index}}" title="{{directory.dir}}">
									<i class="size">[{{directory.size}}]</i> {{directory.sdir}}/
								</label> <br/>
								<div class="files">
									{{#each directory.files as |file|}}	
										<input type="checkbox" name="file_paths[]" value="{{file.path}}" id="lf_file_{{directory.dir}}-{{@index}}" />
										<label for="lf_file_{{directory.dir}}-{{@index}}" title="{{file.path}}">
											<i class="size">[{{file.bytes}}]</i>	{{file.name}}
										</label> <br/>
									{{/each}}
								</div>
							</div>
						{{/each}}
					{{else}}
						 <?php 
							if (! isset($_GET['retry'])) {
								_e('No large files found during this scan.', 'duplicator');
							} else {
								echo "<div style='color:maroon'>";
									_e('No large files found during this scan.  If you\'re having issues building a package click the back button and try '
									. 'adding a file filter to non-essential files paths like wp-content/uploads.   These excluded files can then '
									. 'be manually moved to the new location after you have ran the migration installer.', 'duplicator');
								echo "</div>";
							}
						?>
					{{/if}}
				</div>
			</div>


			<div class="apply-btn" style="margin-bottom:5px;float:right">
				<div class="apply-warn">
					 <?php esc_html_e('*Checking a directory will exclude all items recursively from that path down.  Please use caution when filtering directories.', 'duplicator'); ?>
				</div>
				<button type="button" class="button-small duplicator-quick-filter-btn" disabled="disabled" onclick="Duplicator.Pack.applyFilters(this, 'large')">
					<i class="fa fa-filter fa-sm"></i> <?php esc_html_e('Add Filters &amp; Rescan', 'duplicator');?>
				</button>
				<button type="button" class="button-small" onclick="Duplicator.Pack.showPathsDlg('large')" title="<?php esc_attr_e('Copy Paths to Clipboard', 'duplicator');?>">
					<i class="fa far fa-clipboard" aria-hidden="true"></i>
				</button>
			</div>
			<div style="clear:both"></div>


		</script>
		<div id="hb-files-large-result" class="hb-files-style"></div>
	</div>
</div>

<!-- ======================
ADDON SITES -->
<div id="addonsites-block"  class="scan-item">
	<div class='title' onclick="Duplicator.Pack.toggleScanItem(this);">
		<div class="text"><i class="fa fa-caret-right"></i> <?php esc_html_e('Addon Sites', 'duplicator');?></div>
		<div id="data-arc-status-addonsites"></div>
	</div>
    <div class="info">
        <div style="margin-bottom:10px;">
            <?php
                printf(__('An "Addon Site" is a separate WordPress site(s) residing in subdirectories within this site. If you confirm these to be separate sites, '
					. 'then it is recommended that you exclude them by checking the corresponding boxes below and clicking the \'Add Filters & Rescan\' button.  To backup the other sites '
					. 'install the plugin on the sites needing to be backed-up.'));
            ?>
        </div>
        <script id="hb-addon-sites" type="text/x-handlebars-template">
            <div class="container">
                <div class="hdrs">
                    <span style="font-weight:bold">
                        <?php esc_html_e('Quick Filters', 'duplicator'); ?>
                    </span>
                </div>
                <div class="data">
                    {{#if ARC.FilterInfo.Dirs.AddonSites.length}}
                        {{#each ARC.FilterInfo.Dirs.AddonSites as |path|}}
                        <div class="directory">
                            <input type="checkbox" name="dir_paths[]" value="{{path}}" id="as_dir_{{@index}}"/>
                            <label for="as_dir_{{@index}}" title="{{path}}">
                                {{path}}
                            </label>
                        </div>
                        {{/each}}
                    {{else}}
                    <?php esc_html_e('No add on sites found.'); ?>
                    {{/if}}
                </div>
            </div>
            <div class="apply-btn">
                <div class="apply-warn">
                    <?php esc_html_e('*Checking a directory will exclude all items in that path recursively.'); ?>
                </div>
                <button type="button" class="button-small duplicator-quick-filter-btn" disabled="disabled" onclick="Duplicator.Pack.applyFilters(this, 'addon')">
                    <i class="fa fa-filter fa-sm"></i> <?php esc_html_e('Add Filters &amp; Rescan');?>
                </button>
            </div>
        </script>
        <div id="hb-addon-sites-result" class="hb-files-style"></div>
    </div>
</div>


<!-- ============
FILE NAME CHECKS -->
<div class="scan-item">
	<div class="title" onclick="Duplicator.Pack.toggleScanItem(this);">
		<div class="text"><i class="fa fa-caret-right"></i> <?php esc_html_e('Name Checks', 'duplicator');?></div>
		<div id="data-arc-status-names"></div>
	</div>
	<div class="info">
		<?php
			_e('Unicode and special characters such as "*?><:/\|", can be problematic on some hosts.', 'duplicator');
            esc_html_e('  Only consider using this filter if the package build is failing. Select files that are not important to your site or you can migrate manually.', 'duplicator');
			$txt = __('If this environment/system and the system where it will be installed are set up to support Unicode and long paths then these filters can be ignored.  '
				. 'If you run into issues with creating or installing a package, then is recommended to filter these paths.', 'duplicator');
		?>
		<script id="hb-files-utf8" type="text/x-handlebars-template">
			<div class="container">
				<div class="hdrs">
					<span style="font-weight:bold"><?php esc_html_e('Quick Filters', 'duplicator');?></span>
						<sup><i class="fas fa-question-circle fa-sm" data-tooltip-title="<?php esc_attr_e("Name Checks", 'duplicator'); ?>" data-tooltip="<?php echo esc_attr($txt); ?>"></i></sup>
					<div class='hdrs-up-down'>
						<i class="fa fa-caret-up fa-lg dup-nav-toggle" onclick="Duplicator.Pack.toggleAllDirPath(this, 'hide')" title="<?php esc_attr_e("Hide All", 'duplicator'); ?>"></i>
						<i class="fa fa-caret-down fa-lg dup-nav-toggle" onclick="Duplicator.Pack.toggleAllDirPath(this, 'show')" title="<?php esc_attr_e("Show All", 'duplicator'); ?>"></i>
					</div>
				</div>
				<div class="data">
					<?php _duplicatorGetRootPath();	?>
					{{#if  ARC.FilterInfo.TreeWarning}}
						{{#each ARC.FilterInfo.TreeWarning as |directory|}}
							<div class="directory">
								{{#if directory.count}}
									<i class="fa fa-caret-right fa-lg dup-nav" onclick="Duplicator.Pack.toggleDirPath(this)"></i> &nbsp;
								{{else}}
									<i class="empty"></i>
								{{/if}}
										
								{{#if directory.iscore}}
									<i class="far fa-window-close chk-off" title="<?php esc_attr_e('Core WordPress directories should not be filtered. Use caution when excluding files.', 'duplicator'); ?>"></i>
								{{else}}		
									<input type="checkbox" name="dir_paths[]" value="{{directory.dir}}" id="nc1_dir_{{@index}}" onclick="Duplicator.Pack.filesOff(this)" />
								{{/if}}
								
								<label for="nc1_dir_{{@index}}" title="{{directory.dir}}">
									<i class="count">({{directory.count}})</i>
									{{directory.sdir}}/
								</label> <br/>
								<div class="files">
									{{#each directory.files}}
										<input type="checkbox" name="file_paths[]" value="{{path}}" id="warn_file_{{directory.dir}}-{{@index}}" />
										<label for="warn_file_{{directory.dir}}-{{@index}}" title="{{path}}">
											{{name}}
										</label> <br/>
									{{/each}}
								</div>
							</div>
						{{/each}}
					{{else}}
						<?php esc_html_e('No file/directory name warnings found.', 'duplicator');?>
					{{/if}}
				</di