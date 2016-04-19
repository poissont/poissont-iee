<?php
$projects = getProjectsPublish();
$nb_projets = count($projects);
foreach($projects as $id_project){
	if(get_post_status($id_project) !== false){
		$id_lastprojet = $id_project;
	}
}
$projectspending = getProjectsPending();
$nb_projetspending = count($projectspending);

$lastprojet = get_post($id_lastprojet);
$titre_lastprojet = $lastprojet->post_title;
$funds_raised = getFundsRaised();
$funds_goal = getFundsGoal();
?>
<div class="memberdeck memberdeck-prequel">
	<div class="idc-notofication">
		<?php echo apply_filters('idc_dashboard_notification', null); ?>
	</div>
	<?php include_once IDCE_PATH . 'templates/_mdProfileTabs.php'; ?>
	<ul class="md-box-wrapper full-width cf" id="idc-downloads">
		<li class="md-box <?php echo $p_width; ?>">
			<div class="md-profile">
				<div class="profile-wrapper">
					<div class="md-avatar">
						<?php
						if (is_user_logged_in()) {
							echo get_avatar($current_user->ID, 70);
						}
						?>
					</div>
					<div class="md-fullname">
						<?php
						echo (isset($fname) ? '<span class="md-firstname">' . $fname . '</span>' : '') . " ";
						echo (isset($lname) ? '<span class="md-lastname">' . $lname . '</span>' : '') . "<br>";
						?>
					</div>
					<div class="md-registered">
						Inscrit le <?= date("d/m/Y à h:i", strtotime($registered)) ?> <br>
					</div>
				</div>
				<div class="profile-info-wrapper">

				</div>
			</div>
			</div>
			<div class="md-dash-sidebar">
				<ul>
					<?php ( function_exists('dynamic_sidebar') ? dynamic_sidebar('dashboard-sidebar') : ''); ?>
				</ul>
			</div>
		</li>
		<li class="md-box">
			Nombre de projets publiés : <?= $nb_projets; ?>
		</li>
		<li class="md-box">
			Nombre de projets en attente  : <?= $nb_projetspending; ?>
		</li>
		
		<li class="md-box">
			Titre du dernier projet publié : <?= $titre_lastprojet; ?></li>
		</li>
		<li class="md-box">
			Total des fonds récoltés : <?= $funds_raised; ?> €</li>
		</li>
		<li class="md-box">
			Total des fonds demandés : <?= $funds_goal; ?> €</li>
		</li>
	</ul>

</div>
