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
                        <li class="breadcrumb-item active">{$STATUSES}</li>
                    </ol>
                </div>

                <!-- Update Notification -->
                {include file='includes/update.tpl'}

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h5 style="display:inline">{$CREATING_STATUS}</h5>
                        <div class="float-md-right">
                          <button class="btn btn-warning" onclick="showCancelModal()" type="button">{$CANCEL}</button>
                        </div>
                        <hr />

                        <!-- Success and Error Alerts -->
                        {include file='includes/alerts.tpl'}

                        <form action="" method="post">
                          <div class="form-group">
                            <label for="status_html">{$STATUS_HTML}</label>
                            <input type="text" name="status_html" placeholder="{$STATUS_HTML}" id="status_html" class="form-control">
                          </div>
                          <div class="form-group">
                            <label for="status_forms">{$STATUS_FORMS}</label>
                            <select name="status_forms[]" id="status_forms" size="5" class="form-control" multiple style="overflow:auto;">
                              {if count($ALL_FORMS)}
                                {foreach from=$ALL_FORMS item=item}
                                  <option value="{$item.id}"{if $item.selected} selected{/if}>{$item.name}</option>
                                {/foreach}
                              {/if}
                            </select>
                          </div>
                          <div class="form-group">
                            <label for="status_groups">{$STATUS_GROUPS}</label>
                            <select name="status_groups[]" id="status_groups" size="5" class="form-control" multiple style="overflow:auto;">
                              {if count($ALL_GROUPS)}
                                {foreach from=$ALL_GROUPS item=item}
                                  <option value="{$item.id}"{if $item.selected} selected{/if}>{$item.name}</option>
                                {/foreach}
                              {/if}
                            </select>
                          </div>
                          <div class="form-group">
                            <label for="inputOpen">{$MARKED_AS_OPEN}</label>
                            <input id="inputOpen" name="open" type="checkbox" class="js-switch" />
                          </div>
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

    <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{$ARE_YOU_SURE}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {$CONFIRM_CANCEL}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{$NO}</button>
                    <a href="{$CANCEL_LINK}" class="btn btn-primary">{$YES}</a>
                </div>
            </div>
        </div>
    </div>

{include file='scripts.tpl'}
<script type="text/javascript">
    function showCancelModal(){
        $('#cancelModal').modal().show();
    }
</script>

</body>
</html>