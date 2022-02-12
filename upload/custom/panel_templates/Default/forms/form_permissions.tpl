{include file='header.tpl'}

<body id="page-top">

<!-- Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    {include file='sidebar.tpl'}

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main content -->
        <div id="content">

            <!-- Topbar -->
            {include file='navbar.tpl'}

            <!-- Begin Page Content -->
            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">{$FORMS}</h1>
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{$PANEL_INDEX}">{$DASHBOARD}</a></li>
                        <li class="breadcrumb-item active">{$FORMS}</li>
                    </ol>
                </div>

                <!-- Update Notification -->
                {include file='includes/update.tpl'}

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h5 style="display:inline">{$EDITING_FORM}</h5>
                        <div class="float-md-right">
                            <a href="{$BACK_LINK}" class="btn btn-warning">{$BACK}</a>
                        </div>
                        <hr>
                        
                        <ul class="nav nav-tabs">
                          <li class="nav-item">
                            <a class="nav-link" href="{$GENERAL_SETTINGS_LINK}">{$GENERAL_SETTINGS}</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="{$FIELDS_LINK}">{$FIELDS}</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link active">{$PERMISSIONS}</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="{$STATUSES_LINK}">{$STATUSES}</a>
                          </li>
                        </ul>
                        
                        </br>
                        
                        <!-- Success and Error Alerts -->
                        {include file='includes/alerts.tpl'}
                        
                        <form role="form" action="" method="post">
                            <div class="form-group">
                                <label for="InputGuest">{$SHOW_NAVIGATION_LINK_FOR_GUEST}</label>
                                <input id="inputGuest" name="guest" type="checkbox" class="js-switch"{if $GUEST_VALUE eq 1} checked{/if} />
                            </div>
                                    
                            <script>
                              var groups = [];
                              groups.push("0");
                            </script>
                            <input type="hidden" name="perm-post-0" value="0" />
                            <input type="hidden" name="perm-view_own-0" value="0" />
                            <input type="hidden" name="perm-view_submissions-0" value="0" />
                            <input type="hidden" name="perm-delete_submissions-0" value="0" />
                        
                            <strong>{$USER}</strong>
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th width="20%">{$GROUP}</th>
                                    <th width="40%">{$CAN_POST_SUBMISSION}</th>
                                    <th width="40%">{$CAN_VIEW_OWN_SUBMISSION}</th>
                                </tr>
                                </thead>
                                <tbody>
                                {* Guest first *}
                                <tr>
                                    <td>{$GUESTS}</td>
                                    <td><input type="hidden" name="perm-post-0" value="0" /><input onclick="colourUpdate(this);" name="perm-post-0" id="Input-post-0" value="1" type="checkbox"{if $GUEST_PERMISSIONS|count && $GUEST_PERMISSIONS.0->can_post eq 1} checked{/if} /></td>
                                    <td class="bg-danger"></td>
                                </tr>
                                {foreach from=$GROUP_PERMISSIONS item=group}
                                    <tr>
                                        <td onclick="toggleAll(this);">{$group->name|escape}</td>
                                        <td><input type="hidden" name="perm-post-{$group->id}" value="0" /><input onclick="colourUpdate(this);" name="perm-post-{$group->id}" id="Input-post-{$group->id}" value="1" type="checkbox"{if $group->can_post eq 1} checked{/if} /></td>
                                        <td><input type="hidden" name="perm-view_own-{$group->id}" value="0" /><input onclick="colourUpdate(this);" name="perm-view_own-{$group->id}" id="Input-view_own-{$group->id}" value="1" type="checkbox"{if $group->can_view_own eq 1} checked{/if} /></td>
                                    </tr>
                                    <script>groups.push("{$group->id}");</script>
                                {/foreach}
                                </tbody>
                            </table>
                            <strong>{$STAFFCP}</strong>
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th width="20%">{$GROUP}</th>
                                    <th width="40%">{$CAN_VIEW_SUBMISSIONS}</th>
                                    <th width="40%">{$CAN_DELETE_SUBMISSIONS}</th>
                                </tr>
                                </thead>
                                <tbody>
                                {foreach from=$GROUP_PERMISSIONS item=group}
                                    <tr>
                                        <td onclick="toggleAll(this);">{$group->name|escape}</td>
                                        <td><input type="hidden" name="perm-view_submissions-{$group->id}" value="0" /><input onclick="colourUpdate(this);" name="perm-view_submissions-{$group->id}" id="Input-view_submissions-{$group->id}" value="1" type="checkbox"{if $group->can_view eq 1} checked{/if} /></td>
                                        <td><input type="hidden" name="perm-delete_submissions-{$group->id}" value="0" /><input onclick="colourUpdate(this);" name="perm-delete_submissions-{$group->id}" id="Input-delete_submissions-{$group->id}" value="1" type="checkbox"{if $group->can_delete eq 1} checked{/if} /></td>
                                    </tr>
                                {/foreach}
                                </tbody>
                            </table>
                            <div class="form-group">
                                <input type="hidden" name="token" value="{$TOKEN}">
                                <input type="submit" class="btn btn-primary" value="{$SUBMIT}">
                            </div>
                        </form>
                        
                        {if !isset($PARTYDRAGEN_PREMIUM)}
                        <center><p>Forms Module by <a href="https://partydragen.com/" target="_blank">Partydragen</a></br>Support on <a href="https://discord.gg/TtH6tpp" target="_blank">Discord</a></p></center>
                        {/if}
                    </div>
                </div>

                <!-- Spacing -->
                <div style="height:1rem;"></div>

                <!-- End Page Content -->
            </div>

            <!-- End Main Content -->
        </div>

        {include file='footer.tpl'}

        <!-- End Content Wrapper -->
    </div>

    <!-- End Wrapper -->
</div>

{include file='scripts.tpl'}
<script type="text/javascript">
  function showCancelModal(){
    $('#cancelModal').modal().show();
  }
  function colourUpdate(that) {
    var x = that.parentElement;
    if(that.checked) {
      x.className = "bg-success";
    } else {
      x.className = "bg-danger";
    }
  }
  function toggle(group) {
    if(document.getElementById('Input-post-' + group).checked) {
      document.getElementById('Input-post-' + group).checked = false;
    } else {
      document.getElementById('Input-post-' + group).checked = true;
    }
    if(document.getElementById('Input-view_own-' + group).checked) {
      document.getElementById('Input-view_own-' + group).checked = false;
    } else {
      document.getElementById('Input-view_own-' + group).checked = true;
    }
    if(document.getElementById('Input-view_submissions-' + group).checked) {
      document.getElementById('Input-view_submissions-' + group).checked = false;
    } else {
      document.getElementById('Input-view_submissions-' + group).checked = true;
    }
    if(document.getElementById('Input-delete_submissions-' + group).checked) {
      document.getElementById('Input-delete_submissions-' + group).checked = false;
    } else {
      document.getElementById('Input-delete_submissions-' + group).checked = true;
    }

    colourUpdate(document.getElementById('Input-post-' + group));
    colourUpdate(document.getElementById('Input-view_own-' + group));
    colourUpdate(document.getElementById('Input-view_submissions-' + group));
    colourUpdate(document.getElementById('Input-delete_submissions-' + group));
  }
  for(var g in groups) {
    colourUpdate(document.getElementById('Input-post-' + groups[g]));
    if(groups[g] != "0") {
      colourUpdate(document.getElementById('Input-view_own-' + groups[g]));
      colourUpdate(document.getElementById('Input-view_submissions-' + groups[g]));
      colourUpdate(document.getElementById('Input-delete_submissions-' + groups[g]));
    }
  }
  // Toggle all columns in row
  function toggleAll(that){
    var first = (($(that).parents('tr').find(':checkbox').first().is(':checked') == true) ? false : true);
    $(that).parents('tr').find(':checkbox').each(function(){
      $(this).prop('checked', first);
      colourUpdate(this);
    });
  }
  $(document).ready(function(){
    $('td').click(function() {
      let checkbox = $(this).find('input:checkbox');
      let id = checkbox.attr('id');
      if(checkbox.is(':checked')){
        checkbox.prop('checked', false);
        colourUpdate(document.getElementById(id));
      } else {
        checkbox.prop('checked', true);
        colourUpdate(document.getElementById(id));
      }
    }).children().click(function(e) {
      e.stopPropagation();
    });
  });
</script>

</body>
</html>