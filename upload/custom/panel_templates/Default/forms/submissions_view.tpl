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
			  
                        <!-- Success and Error Alerts -->
                        {include file='includes/alerts.tpl'}

                        <div class="row">
                            <div class="col-md-4"><h4>{$FORM_X}</h4></div>
                            <div class="col-md-4"><h4>{$CURRENT_STATUS_X}</h4></div>
                            <div class="col-md-4"><h4>{$LAST_UPDATED} <span class="pull-right" data-toggle="tooltip" data-original-title="{$LAST_UPDATED_DATE}">{$LAST_UPDATED_FRIENDLY}</span></h4></div>
                        </div>
                        <hr>
				
                        <div class="card">
                          <div class="card-header">
                            {if !empty($USER_AVATAR)}
                              <a href="{$USER_PROFILE}" style="{$USER_STYLE}" target="_blank"><img src="{$USER_AVATAR}" class="rounded" style="max-width:25px;max-height:25px;" alt="{$USER}" /> {$USER}</a>:
                            {else}
                              <i class="fa fa-user"></i> {$USER}:
                            {/if}
                            <span class="pull-right" data-toggle="tooltip" data-original-title="{$CREATED_DATE}">{$CREATED_DATE_FRIENDLY}</span>
                          </div>
                          <div class="card-body">
                            {foreach from=$ANSWERS item=answer}
                            <strong>{$answer.question}</strong>
                            <p>{$answer.answer}</p>
                            {/foreach}
                          </div>
                        </div>
				
                        <h5>{$COMMENTS_TEXT}</h5>
                        {if count($COMMENTS)}
                          {foreach from=$COMMENTS item=comment}
                            <div class="card">
                              <div class="card-header">
                                <a href="{$comment.profile}" style="{$comment.style}" target="_blank"><img src="{$comment.avatar}" class="rounded" style="max-height:25px;max-width:25px;" alt="{$comment.username}" /> {$comment.username}</a>:
                                <span class="pull-right" data-toggle="tooltip" data-original-title="{$comment.date}">{$comment.date_friendly}</span>
                              </div>
                                <div class="card-body">
                                    {$comment.content}
                               </div>
                            </div>
                          {/foreach}
                        {else}
                          {$NO_COMMENTS}
                        {/if}

                        <hr />
                        
                        <form action="" method="post">
                        {if count($STATUSES)}
                        <div class="form-group">
                          {foreach from=$STATUSES item=status}
                          <div class="form-check-inline">
                          <input type="radio" class="form-check-input" name="status" id="{$status.id}" value="{$status.id}"{if $status.active} checked="checked"{/if} {if !$status.permission}disabled{/if}>
                          <label class="form-check-label" for="{$status.id}">{$status.html} </label>
                          </div>
                          {/foreach}
                        </div>
                        {/if}
                        
                          <div class="form-group">
                            <textarea class="form-control" name="content" rows="5" placeholder="{$NEW_COMMENT}"></textarea>
                          </div>
                          <div class="form-group">
                            <input type="hidden" name="token" value="{$TOKEN}">
                            <input type="submit" value="{$SUBMIT}" class="btn btn-primary">
                          </div>
                        </form>

                        <center><p>Forms Module by <a href="https://partydragen.com/" target="_blank">Partydragen</a></p></center>
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