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
                            <a class="nav-link active">{$GENERAL_SETTINGS}</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="{$FIELDS_LINK}">{$FIELDS}</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="{$PERMISSIONS_LINK}">{$PERMISSIONS}</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="{$STATUSES_LINK}">{$STATUSES}</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="{$LIMITS_AND_REQUIREMENTS_LINK}">{$LIMITS_AND_REQUIREMENTS}</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="{$ADVANCED_LINK}">{$ADVANCED}</a>
                          </li>
                        </ul>
                        
                        </br>
                        
                        <!-- Success and Error Alerts -->
                        {include file='includes/alerts.tpl'}
                        
                        <form role="form" action="" method="post">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="InputName">{$FORM_NAME}</label>
                                        <input type="text" name="form_name" class="form-control" id="InputName" placeholder="{$FORM_NAME}" value="{$FORM_NAME_VALUE}">
                                    </div>
                                    <div class="form-group">
                                        <label for="InputUrl">{$FORM_URL}</label>
                                        <input type="text" name="form_url" class="form-control" id="InputURL" placeholder="{$FORM_URL}" value="{$FORM_URL_VALUE}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="InputUrl">{$FORM_ICON}</label>
                                        <input type="text" name="form_icon" class="form-control" id="InputIcon" placeholder="{$FORM_ICON}" value="{$FORM_ICON_VALUE}">
                                    </div>
                                    <div class="form-group">
                                        <label for="link_location">{$FORM_LINK_LOCATION}</label>
                                        <select class="form-control" id="link_location" name="link_location">
                                            <option value="1"{if $LINK_LOCATION_VALUE eq 1} selected{/if}>{$LINK_NAVBAR}</option>
                                            <option value="2"{if $LINK_LOCATION_VALUE eq 2} selected{/if}>{$LINK_MORE}</option>
                                            <option value="3"{if $LINK_LOCATION_VALUE eq 3} selected{/if}>{$LINK_FOOTER}</option>
                                            <option value="4"{if $LINK_LOCATION_VALUE eq 4} selected{/if}>{$LINK_NONE}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                  <div class="form-group">
                                      <label for="inputContent">{$CONTENT}</label>
                                      <textarea name="content" id="inputContent">{$CONTENT_VALUE}</textarea>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group custom-control custom-switch">
                                        <input id="inputCaptcha" name="captcha" type="checkbox" class="custom-control-input"{if $ENABLE_CAPTCHA_VALUE eq 1} checked{/if} />
                                        <label class="custom-control-label" for="inputCaptcha">
                                            {$ENABLE_CAPTCHA}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="token" value="{$TOKEN}">
                                <input type="submit" class="btn btn-primary" value="{$SUBMIT}">
                            </div>
                        </form>
                        
                        </br>
                        
                        <h5 style="display:inline">{$FIELDS}</h5>
                        <div class="float-md-right">
                            <a href="{$NEW_FIELD_LINK}" class="btn btn-primary">{$NEW_FIELD}</a>
                        </div>
                        <hr>
                        {if count($FIELDS_LIST)}
                            {foreach from=$FIELDS_LIST item=field}
                            <div class="row">
                                <div class="col-md-4">
                                    <a href="{$field.edit_link}">{$field.name}</a>
                                </div>
                                <div class="col-md-4">
                                    {$field.type}
                                </div>
                                <div class="col-md-4">
                                    <div class="float-md-right">
                                        <a class="btn btn-warning btn-sm" href="{$field.edit_link}"><i class="fas fa-edit fa-fw"></i></a>
                                        <button class="btn btn-danger btn-sm" type="button" onclick="showDeleteModal('{$field.delete_link}')"><i class="fas fa-trash fa-fw"></i></button>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            {/foreach}
                        {else}
                            {$NONE_FIELDS_DEFINED}
                        {/if}

                        {if !isset($PDS)}
                            <center>
                                <p>Forms Module by <a href="https://partydragen.com/" target="_blank">Partydragen</a></br>Support on <a href="https://discord.gg/TtH6tpp" target="_blank">Discord</a></br>
                                    <a class="ml-1" href="https://partydragen.com/suggestions/" target="_blank" data-toggle="tooltip"
                                       data-placement="top" title="You can submit suggestions here"><i class="fa-solid fa-thumbs-up text-warning"></i></a>
                                    <a class="ml-1" href="https://discord.gg/TtH6tpp" target="_blank" data-toggle="tooltip"
                                       data-placement="top" title="Discord"><i class="fab fa-discord fa-fw text-discord"></i></a>
                                    <a class="ml-1" href="https://partydragen.com/" target="_blank" data-toggle="tooltip"
                                       data-placement="top" title="Website"><i class="fas fa-globe fa-fw text-primary"></i></a>
                                    <a class="ml-1" href="https://www.patreon.com/partydragen" target="_blank" data-toggle="tooltip"
                                       data-placement="top" title="Support the development on Patreon"><i class="fas fa-heart fa-fw text-danger"></i></a>
                                </p>
                            </center>
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
                    {$CONFIRM_DELETE_FIELD}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{$NO}</button>
                    <a href="#" id="deleteLink" class="btn btn-primary">{$YES}</a>
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
</script>

</body>
</html>