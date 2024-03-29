<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('jcalendar');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
   <div class="col-xs-12 eqLogicThumbnailDisplay">
  <legend><i class="fas fa-cog"></i>  {{Gestion}}</legend>
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
  <legend><i class="fas fa-table"></i> {{Mes calendriers}}</legend>
	   <input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
<div class="eqLogicThumbnailContainer">
    <?php
foreach ($eqLogics as $eqLogic) {
	$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
	echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
	echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
	echo '<br>';
	echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
	echo '</div>';
}
?>
</div>
</div>

<div class="col-xs-12 eqLogic" style="display: none;">
		<div class="input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
				<a class="btn btn-default btn-sm eqLogicAction roundedLeft" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a><a class="btn btn-default btn-sm eqLogicAction" data-action="copy"><i class="fas fa-copy"></i> {{Dupliquer}}</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a><a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
			</span>
		</div>
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
    <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
    <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
  </ul>
  <div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
    <div role="tabpanel" class="tab-pane active" id="eqlogictab">
      <br/>
    <form class="form-horizontal">
        <fieldset>
            <div class="form-group">
                <label class="col-sm-3 control-label">{{Nom de l'équipement}}</label>
                <div class="col-sm-3">
                    <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                    <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement template}}"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" >{{Objet parent}}</label>
                <div class="col-sm-3">
                    <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                        <option value="">{{Aucun}}</option>
                        <?php
foreach (jeeObject::all() as $object) {
	echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
}
?>
                   </select>
               </div>
           </div>
	   <div class="form-group">
                <label class="col-sm-3 control-label">{{Catégorie}}</label>
                <div class="col-sm-9">
                 <?php
                    foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                    echo '<label class="checkbox-inline">';
                    echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                    echo '</label>';
                    }
                  ?>
               </div>
           </div>
	<div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-9">
			<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
			<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
		</div>
	</div>


  <div class="form-group">
      <label class="col-sm-3 control-label">{{Géolocalisation}}</label>
      <div class="col-sm-3">
          <select class="form-control eqLogicAttr configuration" id="geoloc" data-l1key="configuration" data-l2key="geoloc" required>
              <?php
              $none = 0;
              if (class_exists('geotravCmd')) {
                  foreach (eqLogic::byType('geotrav') as $geoloc) {
                      if ($geoloc->getConfiguration('type') == 'location') {
                          $none = 1;
                          echo '<option value="' . $geoloc->getId() . '">' . $geoloc->getName() . '</option>';
                      }
                  }
              } 
              if ((config::byKey('info::latitude') != '') && (config::byKey('info::longitude') != '')) {
                  echo '<option value="jeedom">Configuration Jeedom</option>';
                  $none = 1;
              }
              if ($none == 0) {
                  echo '<option value="">Pas de localisation disponible</option>';
              }
              ?>
          </select>
      </div>
  </div>
  <div class="form-group">
      <label class="col-sm-3 control-label">{{Détails des dates hébraïques}}</label>
      <div class="col-sm-3">
          <select class="form-control eqLogicAttr configuration" data-l1key="configuration" data-l2key="hebrewDates">
              <option value="none">{{Aucune}}</option>
              <option value="some">{{Quelques}}</option>
              <option value="entire">{{Toutes}}</option>
          </select>
      </div>
  </div>
  <div class="form-group">
      <label class="col-sm-3 control-label">{{Mode Chabbat et Yomtov}}</label>
      <div class="col-sm-9">
        <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="candleTimes"/>{{Activer}}</label>
      </div> 
  </div>
  <div class="form-group">
      <label class="col-sm-3 control-label">{{Evénément de ce Shabbat}}</label>
      <div class="col-sm-9">
        <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="specialShabbatot"/>{{Activer}}</label>
      </div> 
  </div>
  <div class="form-group">
      <label class="col-sm-3 control-label">{{Parashat de ce Shabbat}}</label>
      <div class="col-sm-9">
        <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="parashatOnSaturday"/>{{Activer}}</label>
      </div> 
  </div>
  <div class="form-group">
      <label class="col-sm-3 control-label">{{Fêtes majeures}}
        <sup>
            <i class="fa fa-question-circle tooltips" title="Attention, la désactivation de cette option entraine une non-récupération de l'événement de ce Shabbat même actif" style="font-size : 1em;color:grey;"></i>
        </sup>
      </label>
      
      <div class="col-sm-9">
        <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="majorHoliday"/>{{Activer}}</label>
      </div>
  </div>

  <div class="form-group">
      <label class="col-sm-3 control-label">{{Fêtes mineures}}</label>
      <div class="col-sm-9">
        <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="minorHoliday"/>{{Activer}}</label>
      </div>
  </div>

  <div class="form-group">
      <label class="col-sm-3 control-label">{{Fêtes modernes}}</label>
      <div class="col-sm-9">
        <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="modernHoliday"/>{{Activer}}</label>
      </div>   
  </div>
  <div class="form-group">
      <label class="col-sm-3 control-label">{{Fêtes Israël et lectures Torah}}</label>
      <div class="col-sm-9">
        <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="holidaysAndTorah"/>{{Activer}}</label>
      </div>
  </div>
  <div class="form-group">
      <label class="col-sm-3 control-label">{{Rosh Chodesh}}</label>
      <div class="col-sm-9">
        <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="roshChodesh"/>{{Activer}}</label>
      </div>
  </div>
  <div class="form-group">
      <label class="col-sm-3 control-label">{{Fêtes}}</label>
      <div class="col-sm-9">
        <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="minorFests"/>{{Activer}}</label>
      </div>     
  </div>

  <div class="form-group">
      <label class="col-sm-3 control-label">{{Jours du Omer}}</label>
      <div class="col-sm-9">
        <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="omerDays"/>{{Activer}}</label>
      </div> 
  </div>
  <div class="form-group">
      <label class="col-sm-3 control-label">{{Affichage en hébreu}}</label>
      <div class="col-sm-9">
        <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="hebrewDisplay"/>{{Activer}}</label>
      </div> 
  </div>
</fieldset>
</form>
</div>
      <div role="tabpanel" class="tab-pane" id="commandtab">
<br/><br/>
<table id="table_cmd" class="table table-bordered table-condensed">
    <thead>
        <tr>
            <th>{{Nom}}</th><th>{{Type}}</th><th>{{Action}}</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
</div>
</div>

</div>
</div>

<?php include_file('desktop', 'jcalendar', 'js', 'jcalendar');?>
<?php include_file('core', 'plugin.template', 'js');?>