<div class="md-requiredlogin login pageconnexion">
		<h3>Cette page n'est acessible qu'aux utilisateurs connectés !</h3>
		<p>Créer un compte, c’est super rapide !
			Grâce à votre compte, vous aurez la possibilité de gérer vos projets et de les modifier.</p>
		
	<div class="vc_col-xs-12 vc_col-md-6">
		
	<p>Vous pouvez aussi vous connecter via le formulaire ci-dessous.</p>
	<?php if (isset($_GET['login_failure']) && $_GET['login_failure'] == 1) {
		if (!isset($_GET['error_code'])) {
			echo '<p class="error">Invalid username or incorrect password</p>';
		}
		else if (isset($_GET['error_code']) && $_GET['error_code'] == "incorrect_password") {
			echo '<p class="error">'.__('Incorrect password', 'memberdeck').'</p>';
		}
		else if (isset($_GET['error_code']) && $_GET['error_code'] == "framework_missing") {
			echo '<p class="error">'.__('Critical Error: IgnitionDeck Framework is missing, please install and activiate IgnitionDeck Framework', 'memberdeck').'</p>';
		}
	} ?>
	<p class="error blank-field <?php echo ((isset($_GET['error_code']) && ($_GET['error_code'] == "empty_password" || $_GET['error_code'] == "empty_username")) ? '' : 'hide') ?>"><?php _e('Username or Password should be not be empty', 'memberdeck') ?></p>
	<?php if (!is_user_logged_in()) { ?>
		<?php
		$durl = md_get_durl();
		$args = array('redirect' => $durl,
			'echo' => false);
		echo wp_login_form($args); ?>
	<p><a class="lostpassword" href="<?php echo site_url(); ?>/wp-login.php?action=lostpassword">Mot de passe oublié</a></p>
	<?php } 
	do_action('idc_below_login_form');
	?>
	
	</div>
	<div class="vc_col-xs-12 vc_col-md-6 firstconnection">
		<h3>Première connexion ?</h3>
		<p><strong>Inscrivez vous sur la page suivante : <a href="<?php bloginfo("url"); ?>/inscription/" class="">Inscription</a></strong></p>
			
	</div>
</div>