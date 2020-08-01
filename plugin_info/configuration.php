<?php
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}
?>
<form class="form-horizontal">
    <fieldset>
        <div class="form-group">
            <label class="col-sm-3 control-label">{{Nombre de minutes après le coucher du soleil}}</label>
            <div class="col-sm-3">
                <input class="configKey form-control" data-l1key="jcalendar-candleAfterSunrise" placeholder="{{Renseigner le nombre de minutes après le coucher du soleil}}" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">{{Nombre de minutes avant le lever du soleil}}</label>
            <div class="col-sm-3">
                <input class="configKey form-control" data-l1key="jcalendar-candleBeforeSunset" placeholder="{{Renseigner le nombre de minutes avant le lever du soleil}}" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">{{URL du site hebcal}}</label>
            <div class="col-sm-3">
                <input class="configKey form-control" data-l1key="jcalendar-url"/>
            </div>
        </div>
        <?php
            $available_languages=array('s' => 'Sephardic transliterations','sh' => 'Sephardic translit. + Hebrew','a' => 'Ashkenazis transliterations','ah' => 'Ashkenazis translit. + Hebrew','h' => 'Hebrew – עברית','fr' => 'French – français','ru' => 'Russian – ру́сский язы́к','pl' => 'Polish – język polski','fi' => 'Finnish – Suomalainen','hu' => 'Hungarian – Magyar nyelv');
        ?>
        <div class="form-group">
            <label class="col-sm-3 control-label">{{Langues d'affichage}}</label>
            <div class="col-sm-3">
                <select class="configKey form-control" data-l1key="jcalendar-languages">
                    <?php
                        foreach ($available_languages as $key => $language) {
                            echo '<option value="'.$key.'">{{'.$language.'}} ( '.$key.' )</option>';
                        }
                    ?>
                </select>
            </div>
        </div>
  </fieldset>
</form>
