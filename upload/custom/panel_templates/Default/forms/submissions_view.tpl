{include file='header.tpl'}
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    {include file='navbar.tpl'}
    {include file='sidebar.tpl'}

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">{$FORMS}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{$PANEL_INDEX}">{$DASHBOARD}</a></li>
                            <li class="breadcrumb-item active">{$FORMS}</li>
							<li class="breadcrumb-item active">{$SUBMISSIONS}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <div class="card">
              <div class="card-body">	
			  
                {if isset($SUCCESS)}
                  <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                    <h5><i class="icon fa fa-check"></i> {$SUCCESS_TITLE}</h5>
                    {$SUCCESS}
                  </div>
                {/if}

                {if isset($ERRORS) && count($ERRORS)}
                  <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5><i class="icon fas fa-exclamation-triangle"></i> {$ERRORS_TITLE}</h5>
                    <ul>
                      {foreach from=$ERRORS item=error}
                        <li>{$error}</li>
                      {/foreach}
                    </ul>
                  </div>
                {/if}

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

              </div>
            </div>

            <!-- Spacing -->
            <div style="height:1rem;"></div>

          </div>
        </section>
    </div>

    {include file='footer.tpl'}

</div>
<!-- ./wrapper -->

{include file='scripts.tpl'}

</body>
</html>