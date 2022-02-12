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
                        <li class="breadcrumb-item active">{$SUBMISSIONS}</li>
                    </ol>
                </div>

                <!-- Update Notification -->
                {include file='includes/update.tpl'}

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h3 style="display:inline;">{$SUBMISSIONS}</h3>
                        <hr>
                        
                        <form action="" method="post">
                          <div class="form-row">
                            <div class="col-2">
                              <label for="form_selection">{$FORM}</label>
                            </div>
                            <div class="col-2">
                              <label for="form_selection">{$STATUS}</label>
                            </div>
                            <div class="col-2">
                              <label for="form_selection">{$USER}</label>
                            </div>
                          </div>
                          
                          <div class="form-row">
                            <div class="col-2">
                              <select class="form-control" id="form_selection" name="form_selection">
                                {foreach from=$FORM_LIST item=form}
                                  <option value="{$form.id}" {if $FORM_SELECTION_VALUE eq {$form.id}} selected{/if}>{$form.name}</option>
                                {/foreach}
                              </select>
                            </div>
                            <div class="col-2">
                              <select class="form-control" id="status_selection" name="status_selection">
                                {foreach from=$STATUS_LIST item=status}
                                  <option value="{$status.id}" {if $STATUS_SELECTION_VALUE eq {$status.id}} selected{/if}>{$status.html}</option>
                                {/foreach}
                              </select>
                            </div>
                            <div class="col-2">
                                <input type="text" name="user" class="form-control" id="InputUser" value="{$USER_VALUE}" placeholder="{$ID_OR_USERNAME}">
                            </div>
                            <div class="col-2">
                              <input type="hidden" name="token" value="{$TOKEN}">
                              <input type="submit" value="{$SORT}" class="btn btn-primary">
                            </div>
                          </div>
                        </form>
                        
                        </br>
                        
                        {if count($SUBMISSIONS_LIST)}
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                      <th>{$FORM}</th>
                                      <th>{$USER}</th>
                                      <th>{$UPDATED_BY}</th>
                                      <th>{$STATUS}</th>
                                      <th>{$ACTIONS}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach from=$SUBMISSIONS_LIST item=submission}
                                    <tr>
                                        <td>{$submission.form_name}</td>
                                        <td>
                                          {if !empty($submission.user_avatar)}
                                            <a href="{$submission.user_profile}" style="{$submission.user_style}"><img src="{$submission.user_avatar}" style="max-height:25px;max-width:25px;" alt="{$submission.user_name}" class="rounded"> {$submission.user_name}</a>
                                          {else}
                                            <i class="fa fa-user"></i> {$submission.user_name}
                                          {/if}
                                          <br /><span data-toggle="tooltip" data-original-title="{$submission.reported_at_full}">{$submission.created_at}</span>
                                        </td>
                                        <td>
                                          {if !empty($submission.updated_by_avatar)}
                                            <a href="{$submission.updated_by_profile}" style="{$submission.updated_by_style}"><img src="{$submission.updated_by_avatar}" style="max-height:25px;max-width:25px;" alt="{$submission.updated_by_name}" class="rounded"> {$submission.updated_by_name}</a>
                                          {else}
                                            <i class="fa fa-user"></i> {$submission.updated_by_name}
                                          {/if}
                                          <br /><span data-toggle="tooltip" data-original-title="{$submission.reported_at_full}">{$submission.updated_at}</span>
                                        </td>
                                        <td><h5>{$submission.status}</h5></td>
                                        <td><a href="{$submission.link}" class="btn btn-primary">{$VIEW} &raquo;</a></td>
                                    </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>
                        {$PAGINATION}
                        {else}
                            {$NO_SUBMISSIONS}
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

{include file='scripts.tpl'}

</body>
</html>