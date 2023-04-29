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

/* Permet la réorganisation des commandes dans l'équipement */
$("#table_cmd").sortable({
  axis: "y",
  cursor: "move",
  items: ".cmd",
  placeholder: "ui-state-highlight",
  tolerance: "intersect",
  forcePlaceholderSize: true
})

// $(".jeeipxv3-led").off().on("change",function(event) {
//   console.log($(this));
// });

/* Fonction permettant l'affichage de l'équipement */
function printEqLogic(eqLogic) {
  // TODO get the proper icon from eqlogic and set it into the IMG attribute
  // console.log(eqLogic);
  //$('#icon_visu').attr('src', 'plugins/reolink/desktop/img/camera' + _eqLogic.id + '.png')
  if (eqLogic.configuration.type != null ) {
    $(".jeeipxv3-root").hide();
  } else {
    $(".jeeipxv3-root").show();
    $("#jeeipxv3-testurlok").hide();
    $("#jeeipxv3-configpushok").hide();

    $("#jeeipxv3-configpush").off().on("click",function(elem) {
      // retrieve ID of configpush command
      idpush = mapEqToCommands[eqLogic.id].configpush;
      jeedom.cmd.execute({
        id: idpush,
        async: false,
        success:  function(newvalue) {
          $("#jeeipxv3-configpushok").show();
          $("#jeeipxv3-configpush").removeClass('btn-default').addClass('btn-success')
        }
      });
    });
    
    $("#jeeipxv3-testurl").off().on("click",function(elem) {
      // retrieve ID of configpush command
      idcmd = mapEqToCommands[eqLogic.id].refreshipx;
      idstatus = mapEqToCommands[eqLogic.id].status;
      jeedom.cmd.execute({
        id: idcmd,
        async: false,
        success:  function(newvalue) {
          console.log('Refresh IPX success');
          jeedom.cmd.execute({
            id: idstatus,
            async: false,
            success:  function(newvalue) {
              console.log('new value '+newvalue);
              $("#jeeipxv3-testurlok").show();
              $("#jeeipxv3-testurl").removeClass('btn-default').addClass('btn-success')
            }
          });
        }
      });
    });

    $.ajax({
      type: "POST",
      url: "plugins/jeeipxv3/core/ajax/jeeipxv3.ajax.php",
      data: {
        eqLogicId: eqLogic.id,
        action: "getAnselectTypes",
      },
      dataType: 'json',
      global: false,
      error: function(error) {
        $('#div_alert').showAlert({ message: error.message, level: 'danger' })
      },
      success: function(data) {
        //console.log(data);
        if (data.state != 'ok') {
          $('#div_alert').showAlert({ message: data.result, level: 'danger' })
          return
        }
        $.each( data.result, function( key, value ) {
          $("#jeeipxv3_"+key).text(value);
        });
        //$('#div_alert').showAlert({ message: 'Ajax call success', level: 'success' })
      }
    });  
  }
}

/* Fonction permettant l'affichage des commandes dans l'équipement */
function addCmdToTable(_cmd) {
  if (!isset(_cmd)) {
    var _cmd = {configuration: {}}
  }
  if (!isset(_cmd.configuration)) {
    _cmd.configuration = {}
  }
  var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">'
  tr += '<td class="hidden-xs">'
  tr += '<span class="cmdAttr" data-l1key="id"></span>'
  tr += '</td>'
  tr += '<td>'
  tr += '<div class="input-group">'
  tr += '<input class="cmdAttr form-control input-sm roundedLeft" data-l1key="name" placeholder="{{Nom de la commande}}">'
  tr += '<span class="input-group-btn"><a class="cmdAction btn btn-sm btn-default" data-l1key="chooseIcon" title="{{Choisir une icône}}"><i class="fas fa-icons"></i></a></span>'
  tr += '<span class="cmdAttr input-group-addon roundedRight" data-l1key="display" data-l2key="icon" style="font-size:19px;padding:0 5px 0 0!important;"></span>'
  tr += '</div>'
  tr += '<select class="cmdAttr form-control input-sm" data-l1key="value" style="display:none;margin-top:5px;" title="{{Commande info liée}}">'
  tr += '<option value="">{{Aucune}}</option>'
  tr += '</select>'
  tr += '</td>'
  tr += '<td>'
  tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>'
  tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>'
  tr += '</td>'
  tr += '<td>'
  tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/>{{Afficher}}</label> '
  tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isHistorized" checked/>{{Historiser}}</label> '
  tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="display" data-l2key="invertBinary"/>{{Inverser}}</label> '
  tr += '<div style="margin-top:7px;">'
  tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">'
  tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">'
  tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="unite" placeholder="Unité" title="{{Unité}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">'
  tr += '</div>'
  tr += '</td>'
  tr += '<td>';
  tr += '<span class="cmdAttr" data-l1key="htmlstate"></span>'; 
  tr += '</td>';
  tr += '<td>'
  if (is_numeric(_cmd.id)) {
    tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fas fa-cogs"></i></a> '
    tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> Tester</a>'
  }
  tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove" title="{{Supprimer la commande}}"></i></td>'
  tr += '</tr>'
  $('#table_cmd tbody').append(tr)
  var tr = $('#table_cmd tbody tr').last()
  jeedom.eqLogic.buildSelectCmd({
    id:  $('.eqLogicAttr[data-l1key=id]').value(),
    filter: {type: 'info'},
    error: function (error) {
      $('#div_alert').showAlert({message: error.message, level: 'danger'})
    },
    success: function (result) {
      tr.find('.cmdAttr[data-l1key=value]').append(result)
      tr.setValues(_cmd, '.cmdAttr')
      jeedom.cmd.changeType(tr, init(_cmd.subType))
    }
  })
}
