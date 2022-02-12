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
                        <h3 style="display:inline;">{$FORMS}</h3>
                        <span class="float-md-right"><a href="{$NEW_FORM_LINK}" class="btn btn-primary">{$NEW_FORM}</a></span>
                        <hr>
                        
                        <!-- Success and Error Alerts -->
                        {include file='includes/alerts.tpl'}
                        
                        <div class="row">
                            <div class="col-md-6">
                                <b>{$FORM}</b>
                            </div>
                            <div class="col-md-6">
                                <span class="float-md-right"><b>{$ACTION}</b></span>
                            </div>
                        </div>
                        {if count($FORMS_LIST)}
                            {foreach from=$FORMS_LIST item=form}
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{$form.edit_link}">{$form.name}</a>
                                </div>
                                <div class="col-md-6">
                                    <div class="float-md-right">
                                        <a class="btn btn-warning btn-sm" href="{$form.edit_link}"><i class="fas fa-edit fa-fw"></i></a>
                                        <button class="btn btn-danger btn-sm" type="button" onclick="showDeleteModal('{$form.delete_link}')"><i class="fas fa-trash fa-fw"></i></button>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            {/foreach}
                        {else}
                            {$NONE_FORMS_DEFINED}
                        {/if}
                    </div>
                </div>
                </br>
                <div class="card">
                    <div class="card-body">
                        <h3 style="display:inline;">{$STATUSES}</h3>
                        <span class="float-md-right"><a href="{$NEW_STATUS_LINK}" class="btn btn-primary">{$NEW_STATUS}</a></span>
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <b>{$STATUS}</b>
                            </div>
                            <div class="col-md-4">
                                <b>{$MARKED_AS_OPEN}</b>
                            </div>
                            <div class="col-md-4">
                                <span class="float-md-right"><b>{$ACTION}</b></span>
                            </div>
                        </div>
                        {if count($STATUS_LIST)}
                            {foreach from=$STATUS_LIST item=status}
                            <div class="row">
                                <div class="col-md-4">
                                    {$status.html}
                                </div>
                                <div class="col-md-4">
                                    {if $status.open eq 1}<i class="fa fa-check-circle text-success"></i>{else}<i class="fa fa-times-circle text-danger"></i>{/if}
                                </div>
                                <div class="col-md-4">
                                    <div class="float-md-right">
                                        <a class="btn btn-warning btn-sm" href="{$status.edit_link}"><i class="fas fa-edit fa-fw"></i></a>
                                        {if $status.id != 1 && $status.id != 2}<button class="btn btn-danger btn-sm" type="button" onclick="showDeleteStatusModal('{$status.delete_link}')"><i class="fas fa-trash fa-fw"></i></button>{else}<button class="btn btn-danger btn-sm" type="button"><i class="fa fa-lock fa-fw"></i></button>{/if}
                                    </div>
                                </div>
                            </div>
                            <hr>
                            {/foreach}
                        {else}
                            {$NONE_FORMS_DEFINED}
                        {/if}

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

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{$ARE_YOU_SURE}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {$CONFIRM_DELETE_FORM}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{$NO}</button>
                    <a href="#" id="deleteLink" class="btn btn-primary">{$YES}</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="deleteStatusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{$ARE_YOU_SURE}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {$CONFIRM_DELETE_STATUS}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{$NO}</button>
                    <a href="#" id="deleteStatusLink" class="btn btn-primary">{$YES}</a>
                </div>
            </div>
        </div>
    </div>

{include file='scripts.tpl'}

<script type="text/javascript">
    function showDeleteModal(id){
        $('#deleteLink').attr('href', id);
        $('#deleteModal').modal().show();
    }
    function showDeleteStatusModal(id){
        $('#deleteStatusLink').attr('href', id);
        $('#deleteStatusModal').modal().show();
    }
</script>

</body>
</html>