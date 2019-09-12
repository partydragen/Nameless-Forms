{include file='header.tpl'}
{include file='navbar.tpl'}

<div class="container">
  <div class="row">
	<div class="col-md-3">
	  {include file='user/navigation.tpl'}
	</div>
	<div class="col-md-9">
	  <div class="card">
		<div class="card-body">
		  <h2 class="card-title">{$SUBMISSIONS}</h2>
		  
			{if isset($SUCCESS)}
			<div class="alert alert-success">
			    {$SUCCESS}
			</div>
			{/if}
			
			{if isset($ERRORS)}
			  <div class="alert alert-danger">
			  {foreach from=$ERRORS item=error}
			    {$error}<br />
			  {/foreach}
			  </div>
			{/if}
		  
			<div class="row">
			  <div class="col-md-4">{$FORM_X}</div>
			  <div class="col-md-4">{$CURRENT_STATUS_X}</div>
			  <div class="col-md-4">{$LAST_UPDATED} <span class="pull-right" data-toggle="tooltip" data-original-title="{$LAST_UPDATED_DATE}">{$LAST_UPDATED_FRIENDLY}</span></div>
			</div>
			<hr>
				
            <div class="card">
              <div class="card-header">
				<a href="{$USER_PROFILE}" style="{$USER_STYLE}" target="_blank"><img src="{$USER_AVATAR}" class="rounded" style="max-width:25px;max-height:25px;" alt="{$USER}" /> {$USER}</a>:
                <span class="pull-right" data-toggle="tooltip" data-original-title="{$CREATED_DATE}">{$CREATED_DATE_FRIENDLY}</span>
              </div>
              <div class="card-body">
				{foreach from=$ANSWERS item=answer}
				<strong>{$answer.question}</strong>
				<p>{$answer.answer}</p>
				{/foreach}
              </div>
            </div>
				
			</br>
				
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
				</br>
			  {/foreach}
            {else}
			  {$NO_COMMENTS}
			  </br>
            {/if}
			
			{if $CAN_COMMENT}
            <form action="" method="post">
              <div class="form-group">
                <textarea class="form-control" name="content" rows="5" placeholder="{$NEW_COMMENT}"></textarea>
              </div>
              <div class="form-group">
                <input type="hidden" name="token" value="{$TOKEN}">
                <input type="submit" value="{$SUBMIT}" class="btn btn-primary">
              </div>
            </form>
			{/if}
		  
		</div>
	  </div>
	</div>
  </div>
</div>
{include file='footer.tpl'}