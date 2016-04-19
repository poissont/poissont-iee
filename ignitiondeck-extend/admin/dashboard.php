<?php do_action("bii_options_submit"); ?>
<div class="bii_dashboard">
	<div class="message"><?php
		if (isset($_SESSION["bii_message"])) {
			echo $_SESSION["bii_message"];
		}
		?></div>
	<div class="titre ">
		<h1 class="faa-parent animated-hover"><span class="fa fa-rocket faa-passing"></span> Plugin Initiondeskextend version <?= IDCExtend_version; ?></h1>

	</div>
	<div class="col-xxs-12 col-md-8">
		<div class="col-xxs-12">
			<div class="meta-box-holder">
				<ul class="nav nav-tabs bii-option-title">
					<?php do_action("bii_options_title"); ?> 
				</ul>
				<form method="post" id="poststuff" action="<?= get_admin_url(); ?>admin.php?page=<?= global_class::wp_nom_menu(); ?>">
					<?php do_action("bii_options"); ?> 
					<?php if ($_SERVER["REMOTE_ADDR"] == "77.154.194.84") { ?>
						<div class="col-xxs-12 pl-zdt bii_option hidden">
							<h2 class="faa-parent animated-hover"><i class="fa fa-cogs faa-ring"></i> Zone de test</h2>
							<?php
							pre(apply_filters("bii_class_menu", ""));
							pre(apply_filters("idc_class_containerwrapper", "post", true));
							?>
						</div>
					<?php } ?>
					<button class="publier btn btn-success hidden" accesskey="p" tabindex="5"><span class="fa fa-save"></span> Enregistrer les modifications</button>
				</form>
			</div>
		</div>

	</div>

</div>

