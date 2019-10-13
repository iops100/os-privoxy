{#
 # Copyright (C) 2018 Damien Vargas
 # Copyright (C) 2017 Frank Wall
 # Copyright (C) 2014-2015 Deciso B.V.
 # All rights reserved.
 #
 # Redistribution and use in source and binary forms, with or without modification,
 # are permitted provided that the following conditions are met:
 #
 # 1.  Redistributions of source code must retain the above copyright notice,
 #     this list of conditions and the following disclaimer.
 #
 # 2.  Redistributions in binary form must reproduce the above copyright notice,
 #     this list of conditions and the following disclaimer in the documentation
 #     and/or other materials provided with the distribution.
 #
 # THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
 # INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
 # AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 # AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 # OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 # SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 # INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 # CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 # ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 # POSSIBILITY OF SUCH DAMAGE.
 #}

<script>
    $( document ).ready(function () {
        var data_get_map = {'frm_miscfiles_settings':"/api/privoxy/miscfiles/get"};
        mapDataToFormUI(data_get_map).done(function (data) {
            formatTokenizersUI();
            $('.selectpicker').selectpicker('refresh');
            updateServiceControlUI('privoxy');
            isSubsystemDirty();
        });

        ajaxCall(url="/api/privoxy/service/status", sendData={}, callback=function (data, status) {
            updateServiceStatusUI(data['status']);
            isSubsystemDirty();
        });

        /*************************************************************************************************************
         * link grid actions
         *************************************************************************************************************/

        $("#grid-miscfiles").UIBootgrid(
            {   'search':'/api/privoxy/miscfiles/searchMiscFiles',
                'get':'/api/privoxy/miscfiles/getMiscFiles/',
                'set':'/api/privoxy/miscfiles/setMiscFiles/',
                'add':'/api/privoxy/miscfiles/addMiscFiles/',
                'del':'/api/privoxy/miscfiles/delMiscFiles/',
                'toggle':'/api/privoxy/miscfiles/toggleMiscFiles/'
            }
        );

        /*************************************************************************************************************
         * Commands
         *************************************************************************************************************/
		/**
	       * get the isSubsystemDirty value and print a notice
	       */
	      function isSubsystemDirty() {
	         ajaxGet(url="/api/privoxy/service/dirty", sendData={}, callback=function(data,status) {
	            if (status == "success") {
	               if (data.privoxy.dirty === true) {
	                  $("#configChangedMsg").removeClass("hidden");
	               } else {
	                  $("#configChangedMsg").addClass("hidden");
	               }
	            }
	         });
	      }
	      
	      /**
	       * chain std_bootgrid_reload from opnsense_bootgrid_plugin.js
	       * to get the isSubsystemDirty state on "UIBootgrid" changes
	       */
	      var opn_std_bootgrid_reload = std_bootgrid_reload;
	      std_bootgrid_reload = function(gridId) {
	         opn_std_bootgrid_reload(gridId);
	         isSubsystemDirty();
	      };
      
        /**
         * Reconfigure
         */        
        $("#btnApplyConfig").unbind('click').click(function(){
            $("#btnApplyConfigProgress").addClass("fa fa-spinner fa-pulse");
            ajaxCall(url="/api/privoxy/service/reconfigure", sendData={}, callback=function(data,status) {
                if (status != "success" || data['status'] != 'ok') {
                    BootstrapDialog.show({
                        type: BootstrapDialog.TYPE_WARNING,
                        title: "{{ lang._('Error reconfiguring Privoxy') }}",
                        message: data['status'],
                        draggable: true
                    });
                    //On stoppe le pulse
                    $("#btnApplyConfigProgress").removeClass("fa fa-spinner fa-pulse");
                } else {
                	//On stoppe le pulse
                	$("#btnApplyConfigProgress").removeClass("fa fa-spinner fa-pulse");
                	//On cache le bouton
                	$('#btnApplyConfig').blur();
                	//On valide le nettoyage
                	isSubsystemDirty();
                }
            });
        });
        
        // update history on tab state and implement navigation
    	if(window.location.hash != "") {
        	$('a[href="' + window.location.hash + '"]').click()
    	}
    	$('.nav-tabs a').on('shown.bs.tab', function (e) {
        	history.pushState(null, null, e.target.hash);
    	});
    });
</script>

<div class="alert alert-info hidden" role="alert" id="configChangedMsg">
   <button class="btn btn-primary pull-right" id="btnApplyConfig" type="button"><b>{{ lang._('Apply changes') }}</b> <i id="btnApplyConfigProgress"></i></button>
   {{ lang._('The Privoxy configuration has been changed') }} <br /> {{ lang._('You must apply the changes in order for them to take effect.')}}
</div>

<div class="tab-content content-box tab-content">
    <div class="content-box" style="padding-bottom: 1.5em;">
    	<!-- tab page "miscfiles" -->
        <table id="grid-miscfiles" class="table table-condensed table-hover table-striped table-responsive" data-editDialog="MiscFiles">
            <thead>
            <tr>
                <th data-column-id="ruleType" data-type="string" data-visible="true" data-sortable="false">{{ lang._('Rule Type') }}</th>
                <th data-column-id="targetFile" data-type="string" data-visible="true" data-sortable="false">{{ lang._('File Name') }}</th>
                <th data-column-id="comment" data-type="string" data-visible="true" data-sortable="false">{{ lang._('Comment') }}</th>
                <th data-column-id="uuid" data-type="string" data-identifier="true" data-visible="false" data-sortable="false">{{ lang._('ID') }}</th>
                <th data-column-id="commands" data-formatter="commands" data-sortable="false">{{ lang._('Commands') }}</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
            <tr>
                <td></td>
                <td>
                    <button data-action="add" type="button" class="btn btn-xs btn-default"><span class="fa fa-plus"></span></button>
                </td>
            </tr>
            </tfoot>
        </table>
        </div>
        {{ partial("layout_partials/base_dialog",['fields':formMiscFiles,'id':'MiscFiles','label':lang._('Add Misc Files')])}}
</div>

