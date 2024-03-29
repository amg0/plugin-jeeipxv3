<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
// Déclaration des variables obligatoires
$plugin = plugin::byId('jeeipxv3');
$eqLogics = eqLogic::byType($plugin->getId());
$mapEqToCommands = array();

$cmds = [
	'configpush'=> 'action',
	'refresh'=> 'action',
	'status' => 'info'
];

foreach ($eqLogics as $eqLogic) {
	if (is_null( $eqLogic->getConfiguration('type',null) )) {
		$id = $eqLogic->getId();
		$mapEqToCommands[$id] = array();
		foreach ($cmds as $key=>$value ) {
			$cmd = $eqLogic->getCmd($value, $key);
			if (is_object($cmd)) {
				$mapEqToCommands[$id][$key] = $cmd->getId();
			}
		}
	}
}	
sendVarToJS('eqType', $plugin->getId());
sendVarToJS('mapEqToCommands', $mapEqToCommands);
?>

<div class="row row-overflow">
	<!-- Page d'accueil du plugin -->
	<div class="col-xs-12 eqLogicThumbnailDisplay">
		<legend><i class="fas fa-cog"></i>  {{Gestion}}</legend>
		<!-- Boutons de gestion du plugin -->
		<div class="eqLogicThumbnailContainer">
			<div class="cursor eqLogicAction logoPrimary" data-action="add">
				<i class="fas fa-plus-circle"></i>
				<br>
				<span>{{Ajouter}}</span>
			</div>
			<div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
				<i class="fas fa-wrench"></i>
				<br>
				<span>{{Configuration}}</span>
			</div>
		</div>
		<legend><i class="fas fa-table"></i> {{Mes équipements}}</legend>
		<?php
		if (count($eqLogics) == 0) {
			echo '<br><div class="text-center" style="font-size:1.2em;font-weight:bold;">{{Aucun équipement Template trouvé, cliquer sur "Ajouter" pour commencer}}</div>';
		} else {
			// Champ de recherche
			echo '<div class="input-group" style="margin:5px;">';
			echo '<input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic">';
			echo '<div class="input-group-btn">';
			echo '<a id="bt_resetSearch" class="btn" style="width:30px"><i class="fas fa-times"></i></a>';
			echo '<a class="btn roundedRight hidden" id="bt_pluginDisplayAsTable" data-coreSupport="1" data-state="0"><i class="fas fa-grip-lines"></i></a>';
			echo '</div>';
			echo '</div>';
			// Liste des équipements du plugin
			echo '<div class="eqLogicThumbnailContainer">';
			foreach ($eqLogics as $eqLogic) {
				$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
				echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
				echo '<img src="' . $eqLogic->getImage() . '"/>';
				echo '<br>';
				echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
				echo '<span class="hiddenAsCard displayTableRight hidden">';
				echo ($eqLogic->getIsVisible() == 1) ? '<i class="fas fa-eye" title="{{Equipement visible}}"></i>' : '<i class="fas fa-eye-slash" title="{{Equipement non visible}}"></i>';
				echo '</span>';
				echo '</div>';
			}
			echo '</div>';
		}
		?>
	</div> <!-- /.eqLogicThumbnailDisplay -->

	<!-- Page de présentation de l'équipement -->
	<div class="col-xs-12 eqLogic" style="display: none;">
		<!-- barre de gestion de l'équipement -->
		<div class="input-group pull-right" style="display:inline-flex;">
			<span class="input-group-btn">
				<!-- Les balises <a></a> sont volontairement fermées à la ligne suivante pour éviter les espaces entre les boutons. Ne pas modifier -->
				<a class="btn btn-sm btn-default eqLogicAction roundedLeft" data-action="configure"><i class="fas fa-cogs"></i><span class="hidden-xs"> {{Configuration avancée}}</span>
				</a><a class="btn btn-sm btn-default eqLogicAction" data-action="copy"><i class="fas fa-copy"></i><span class="hidden-xs">  {{Dupliquer}}</span>
				</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}
				</a><a class="btn btn-sm btn-danger eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}
				</a>
			</span>
		</div>
		<!-- Onglets -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
			<li role="presentation"><a href="#commandtab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-list"></i> {{Commandes}}</a></li>
		</ul>
		<div class="tab-content">
			<!-- Onglet de configuration de l'équipement -->
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
				<!-- Partie gauche de l'onglet "Equipements" -->
				<!-- Paramètres généraux et spécifiques de l'équipement -->
				<form class="form-horizontal">
					<fieldset>
						<div class="col-lg-6">
							<legend><i class="fas fa-wrench"></i> {{Paramètres généraux}}</legend>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Nom de l'équipement}}</label>
								<div class="col-sm-6">
									<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display:none;">
									<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement}}">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label" >{{Objet parent}}</label>
								<div class="col-sm-6">
									<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
										<option value="">{{Aucun}}</option>
										<?php
										$options = '';
										foreach ((jeeObject::buildTree(null, false)) as $object) {
											$options .= '<option value="' . $object->getId() . '">' . str_repeat('&nbsp;&nbsp;', $object->getConfiguration('parentNumber')) . $object->getName() . '</option>';
										}
										echo $options;
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Catégorie}}</label>
								<div class="col-sm-6">
									<?php
									foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
										echo '<label class="checkbox-inline">';
										echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" >' . $value['name'];
										echo '</label>';
									}
									?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Options}}</label>
								<div class="col-sm-6">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked>{{Activer}}</label>
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked>{{Visible}}</label>
								</div>
							</div>

							<legend><i class="fas fa-cogs"></i> {{Paramètres spécifiques}}</legend>
							<div class="jeeipxv3-root">
								<div class="form-group">
									<label class="col-sm-4 control-label">{{IP}}
										<sup><i class="fas fa-question-circle tooltips" title="{{Renseignez l adresse IP de l'équipement}}"></i></sup>
									</label>
									<div class="col-sm-6">
										<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="ipaddr" placeholder="{{Adresse IP}}">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Port}}
										<sup><i class="fas fa-question-circle tooltips" title="{{Renseignez le port de l'équipement}}"></i></sup>
									</label>
									<div class="col-sm-6">
										<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="port" placeholder="{{Port}}">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{User Name}}
										<sup><i class="fas fa-question-circle tooltips" title="{{Renseignez l utilisateur de l'équipement}}"></i></sup>
									</label>
									<div class="col-sm-6">
										<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="username" placeholder="{{Utilisateur}}">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label"> {{Mot de passe}}
										<sup><i class="fas fa-question-circle tooltips" title="{{Renseignez le mot de passe}}"></i></sup>
									</label>
									<div class="col-sm-6">
										<input type="text" class="eqLogicAttr form-control inputPassword" data-l1key="configuration" data-l2key="password">
									</div>
								</div>
							</div>
							<legend><i class="fas fa-play"></i> {{Actions de Préparation}}</legend>
							<div class="jeeipxv3-root">
								<div class="alert alert-info col-xs-10 col-xs-offset-1">
									<i class="fas fa-info"></i>
									Sauver les parametres avant d'utiliser ces actions.
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Actions</label>
									<div class="col-sm-6 btn-group" role="group" aria-label="...">
										<button id="jeeipxv3-testurl" type="button" class="btn btn-default"><i id="jeeipxv3-testurlok" class="fas fa-check"></i> Test Access</button>
										&nbsp;
										<button id="jeeipxv3-configpush" type="button" class="btn btn-default"><i id="jeeipxv3-configpushok" class="fas fa-check"></i> Config Push</button>
									</div>
								</div>
							</div>
						</div>

						<!-- Partie droite de l'onglet "Équipement" -->
						<!-- Affiche un champ de commentaire par défaut mais vous pouvez y mettre ce que vous voulez -->
						<div class="col-lg-6">
							<legend><i class="fas fa-info"></i> {{Informations}}</legend>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Description}}</label>
								<div class="col-sm-6">
									<textarea class="form-control eqLogicAttr autogrow" data-l1key="comment"></textarea>
								</div>
							</div>
						</div>

						<div class="col-lg-6 jeeipxv3-root">
							<legend><i class="fas fa-cogs"></i> {{Configuration}}</legend>
							<div class="alert alert-warning col-xs-10 col-xs-offset-1">
								<i class="fas fa-exclamation-triangle"></i>
								Décocher une ou plusieurs cases aura pour conséquence la suppression du ou des équipements correspondants.
							</div>
							<div class="form-group">
							<div class="col-sm-1"></div>
								<div class="col-sm-9">
									<!-- Nav tabs -->
									<ul class="nav nav-tabs" role="tablist">
										<li role="presentation" class="active"><a href="#output" aria-controls="output" role="tab" data-toggle="tab">Output</a></li>
										<li role="presentation"><a href="#input" aria-controls="input" role="tab" data-toggle="tab">Inputs</a></li>
										<li role="presentation"><a href="#analogic" aria-controls="analogic" role="tab" data-toggle="tab">Analogic</a></li>
										<li role="presentation"><a href="#counter" aria-controls="counter" role="tab" data-toggle="tab">Counter</a></li>
									</ul>

									<!-- Tab panes -->
									<div class="tab-content">
										<div role="tabpanel" class="tab-pane active" id="output">
											<?php 
											for ($i=0; $i<32; $i++) { 
												echo '<label class="checkbox-inline">';
												echo '<input class="jeeipxv3-led eqLogicAttr" type="checkbox" data-l1key="configuration" data-l2key="led'.$i.'" id="led'.$i.'" value="led'.$i.'" />'; 
												echo 'led'.$i;
												echo '</label>';
											} 
											?>
										</div>
										<div role="tabpanel" class="tab-pane" id="input">
											<?php 
												for ($i=0; $i<32; $i++) { 
													echo '<label class="checkbox-inline">';
													echo '<input class="jeeipxv3-btn eqLogicAttr" type="checkbox" data-l1key="configuration" data-l2key="btn'.$i.'" id="btn'.$i.'" value="btn'.$i.'" />'; 
													echo 'btn'.$i;
													echo '</label>';
												} 
											?>
										</div>
										<div role="tabpanel" class="tab-pane" id="analogic">
											<?php 
												for ($i=0; $i<16; $i++) { 
													echo '<label class="checkbox-inline">';
													echo '<input class="jeeipxv3-btn eqLogicAttr" type="checkbox" data-l1key="configuration" data-l2key="analog'.$i.'" id="analog'.$i.'" value="analog'.$i.'" />'; 
													echo 'analog'.$i;
													echo ' : <span id="jeeipxv3_analog'.$i.'">?</span>';
													echo '</label><br>';
												} 
											?>										
										</div>
										<div role="tabpanel" class="tab-pane" id="counter">
										<?php 
												for ($i=0; $i<8; $i++) { 
													echo '<label class="checkbox-inline">';
													echo '<input class="jeeipxv3-btn eqLogicAttr" type="checkbox" data-l1key="configuration" data-l2key="count'.$i.'" id="count'.$i.'" value="count'.$i.'" />'; 
													echo 'count'.$i;
													echo '</label>';
												} 
											?>	
										</div>
									</div>
								</div>
							</div>
						</div>

					</fieldset>
				</form>
			</div><!-- /.tabpanel #eqlogictab-->

			<!-- Onglet des commandes de l'équipement -->
			<div role="tabpanel" class="tab-pane" id="commandtab">
				<a class="btn btn-default btn-sm pull-right cmdAction" data-action="add" style="margin-top:5px;"><i class="fas fa-plus-circle"></i> {{Ajouter une commande}}</a>
				<br><br>
				<div class="table-responsive">
					<table id="table_cmd" class="table table-bordered table-condensed">
						<thead>
							<tr>
								<th class="hidden-xs" style="min-width:50px;width:70px;">ID</th>
								<th style="min-width:200px;width:350px;">{{Nom}}</th>
								<th>{{Type}}</th>
								<th style="min-width:260px;">{{Options}}</th>
								<th>{{Etat}}</th>
								<th style="min-width:80px;width:200px;">{{Actions}}</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div><!-- /.tabpanel #commandtab-->

		</div><!-- /.tab-content -->
	</div><!-- /.eqLogic -->
</div><!-- /.row row-overflow -->

<!-- Inclusion du fichier javascript du plugin (dossier, nom_du_fichier, extension_du_fichier, id_du_plugin) -->
<?php include_file('desktop', 'jeeipxv3', 'js', 'jeeipxv3');?>
<!-- Inclusion du fichier javascript du core - NE PAS MODIFIER NI SUPPRIMER -->
<?php include_file('core', 'plugin.template', 'js');?>
