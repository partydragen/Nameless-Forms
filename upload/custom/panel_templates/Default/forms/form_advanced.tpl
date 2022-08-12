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
                            <a class="nav-link" href="{$PERMISSIONS_LINK}">{$PERMISSIONS}</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="{$STATUSES_LINK}">{$STATUSES}</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link active" href="{$ADVANCED_LINK}">{$ADVANCED}</a>
                          </li>
                        </ul>
                        
                        </br>
                        
                        <!-- Success and Error Alerts -->
                        {include file='includes/alerts.tpl'}

                        <div class="alert alert-warning" role="alert">
                          These features is currently for patreon supporters, it will be available for everyone in the future with means this wont function for you
                          </br></br>
                          <a href="https://partydragen.com/patreon/" target="_blank" class="btn btn-primary">Patreon</a>
                        </div>

                        <form role="form" action="" method="post">
                            <div class="form-group">
                                <label for="inputSubmissionSource">{$SUBMISSION_SOURCE}</label>
                                <select class="form-control" id="inputSubmissionSource" name="submission_source">
                                    {foreach from=$SUBMISSION_SOURCE_LIST item=source}
                                        <option value="{$source.value}"{if $SUBMISSION_SOURCE_VALUE eq {$source.value}} selected{/if}>{$source.name}</option>
                                    {/foreach}
                                </select>
                            </div>
                            {if isset($SUBMIT_TO_FORUM)}
                            <div class="form-group">
                                <label for="inputForum">Submit submission to forum?</label>
                                <select class="form-control" id="inputForum" name="forum">
                                    {foreach from=$SUBMIT_TO_FORUM_LIST item=forum}
                                        <option value="{$forum.id}"{if $SUBMIT_TO_FORUM_VALUE eq {$forum.id}} selected{/if}>{$forum.id} - {$forum.title}</option>
                                    {/foreach}
                                </select>
                            </div>
                            {/if}
                            <div class="form-group">
                                <input type="hidden" name="token" value="{$TOKEN}">
                                <!--<input type="submit" class="btn btn-primary" value="{$SUBMIT}" disabled>-->
                                <span data-toggle="popover" data-title="Early access for patreons" data-content="This feature is currently in early access for patreon supporters" data-placement="right"><a href="#" class="btn btn-primary disabled">{$SUBMIT}</a></span>
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

</body>
</html>