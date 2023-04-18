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
                            <div class="col-md-4"><h4>{$LAST_UPDATED} <span data-toggle="tooltip" data-original-title="{$LAST_UPDATED_DATE}">{$LAST_UPDATED_FRIENDLY}</span></h4></div>
                        </div>
                        <hr>
                
                        <div class="card">
                          <div class="card-header">
                            {if !empty($USER_AVATAR)}
                              <a href="{$USER_PROFILE}" style="{$USER_STYLE}" target="_blank"><img src="{$USER_AVATAR}" class="rounded" style="max-width:25px;max-height:25px;" alt="{$USER}" /> {$USER}</a>:
                            {else}
                              <i class="fa fa-user"></i> {$USER}:
                            {/if}
                            <span data-toggle="tooltip" data-original-title="{$CREATED_DATE}">{$CREATED_DATE_FRIENDLY}</span>
                            {if $DELETE_LINK}
                                <button class="btn btn-danger btn-sm float-right" type="button" onclick="showDeleteSubmissionModal('{$DELETE_LINK}')"><i class="fas fa-trash fa-fw"></i></button>
                            {/if}
                          </div>
                          <div class="card-body">
                            {foreach from=$ANSWERS item=answer}
                            <strong>{$answer.question}</strong>
                            {if $answer.field_type != 10}
                              <p>{$answer.answer}</p>
                            {else}
                              <p><a href="{$PATH_TO_UPLOADS}{$answer.answer}" target="_blank">{$answer.answer}</a></p>
                            {/if}
                            {/foreach}
                          </div>
                        </div>
                
                        <h5>{$COMMENTS_TEXT}</h5>
                        {if count($COMMENTS)}
                          {foreach from=$COMMENTS item=comment}
                            <div class="card">
                              <div class="card-header">
                                <a href="{$comment.profile}" style="{$comment.style}" target="_blank"><img src="{$comment.avatar}" class="rounded" style="max-height:25px;max-width:25px;" alt="{$comment.username}" /> {$comment.username}</a>:
                                <span data-toggle="tooltip" data-original-title="{$comment.date}">{$comment.date_friendly}</span> {if $comment.anonymous} <span class="badge badge-secondary">{$ANONYMOUS}</span>{/if} {if $comment.staff_only} <span class="badge badge-secondary">{$STAFF_ONLY}</span>{/if}
                                {if $comment.delete_link}
                                    <button class="btn btn-danger btn-sm float-right" type="button" onclick="showDeleteCommentModal('{$comment.delete_link}')"><i class="fas fa-trash fa-fw"></i></button>
                                {/if}
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
                            <textarea class="form-control" name="content" rows="5" placeholder="{$NEW_COMMENT}">{$COMMENT_VALUE}</textarea>
                          </div>
                          {if $CAN_USE_ANONYMOUS}
                            <div class="form-group custom-control custom-switch">
                              <input id="inputAnonymous" name="anonymous" type="checkbox" class="custom-control-input" />
                              <label class="custom-control-label" for="inputAnonymous">{$SUBMIT_AS_ANONYMOUS}</label>
                            </div>
                          {/if}
                          {if $CAN_SEND_EMAIL}
                            <div class="form-group custom-control custom-switch">
                              <input id="inputNotifyEmail" name="notify_email" type="checkbox" class="custom-control-input" />
                              <label class="custom-control-label" for="inputNotifyEmail">{$SEND_NOTIFY_EMAIL}</label>
                            </div>
                          {/if}
                          <div class="form-group custom-control custom-switch">
                            <input id="inputStaffOnly" name="staff_only" type="checkbox" class="custom-control-input" />
                            <label class="custom-control-label" for="inputStaffOnly">{$COMMENT_STAFF_ONLY}</label>
                          </div>
                          <div class="form-group">
                            <input type="hidden" name="token" value="{$TOKEN}">
                            <input type="submit" value="{$SUBMIT}" class="btn btn-primary">
                          </div>
                        </form>

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

<div class="modal fade" id="deleteSubmissionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{$ARE_YOU_SURE}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {$CONFIRM_DELETE_SUBMISSION}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{$NO}</button>
                <a href="#" id="deleteSubmissionLink" class="btn btn-primary">{$YES}</a>
            </div>
        </div>
    </div>
</div>
    
<div class="modal fade" id="deleteCommentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{$ARE_YOU_SURE}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {$CONFIRM_DELETE_COMMENT}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{$NO}</button>
                <a href="#" id="deleteCommentLink" class="btn btn-primary">{$YES}</a>
            </div>
        </div>
    </div>
</div>

{include file='scripts.tpl'}

<script type="text/javascript">
    function showDeleteSubmissionModal(id){
        $('#deleteSubmissionLink').attr('href', id);
        $('#deleteSubmissionModal').modal().show();
    }
    function showDeleteCommentModal(id){
        $('#deleteCommentLink').attr('href', id);
        $('#deleteCommentModal').modal().show();
    }
</script>

</body>
</html>