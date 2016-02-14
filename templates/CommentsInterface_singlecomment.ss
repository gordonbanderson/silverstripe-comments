<div class="commentWrapper $SpamClass">
<% if $Gravatar %>
    <img class="gravatar" src="$Gravatar.ATT" alt="Gravatar for $Name.ATT" title="Gravatar for $Name.ATT" />
<% end_if %>
<div class="comment-text<% if $Gravatar %> hasGravatar<% end_if %>" id="<% if $isPreview %>comment-preview<% else %>$Permalink<% end_if %>">
    <% if not $isPreview %>
    <p class="info" id="$Permalink">
        <% if $URL %>
            <a class="author" href="$URL.URL" rel="nofollow">$AuthorName.XML</a>
        <% else %>
            <span class="author">$AuthorName.XML</span>
        <% end_if %>
        <span class="date">$Created.Nice <% if $JavaScriptEnabled %><time class="timeago" datetime="$Created.format(c)">$Created.Nice</time><% end_if %></span>
    </p>
<% end_if %>
	<div class="commentContent">$EscapedComment</div>
    <% if $RepliesEnabled %>
        <div class="replyButtonContainer">
        <% if $JavaScriptEnabled %>
            <a class="comment-reply-link btn" href="#{$ReplyForm.FormName}">Reply to $AuthorName.XML</a>
        <% else %>
            <a class="comment-reply-link btn" href="$Parent.Link?replyTo=$ID#Form_ReplyForm_$ID">Reply to $AuthorName.XML</a>
        <% end_if %>
        </div>
        <% include CommentReplyForm %>
    <% end_if %>
</div>

<% if not $isPreview %>
	<% if $ApproveLink || $SpamLink || $HamLink || $DeleteLink || $RepliesEnabled %>
		<div class="comment-action-links">
			<div class="comment-moderation-options">
				<% if $ApproveLink %>
					<a href="$ApproveLink.ATT" class="approve"><% _t('CommentsInterface_singlecomment_ss.APPROVE', 'approve it') %></a>
				<% end_if %>
				<% if $SpamLink %>
					<a href="$SpamLink.ATT" class="spam"><% _t('CommentsInterface_singlecomment_ss.ISSPAM','spam it') %></a>
				<% end_if %>
				<% if $HamLink %>
					<a href="$HamLink.ATT" class="ham"><% _t('CommentsInterface_singlecomment_ss.ISNTSPAM','not spam') %></a>
				<% end_if %>
				<% if $DeleteLink %>
					<a href="$DeleteLink.ATT" class="delete"><% _t('CommentsInterface_singlecomment_ss.REMCOM','reject it') %></a>
				<% end_if %>
			</div>
			<% if $RepliesEnabled %>
                <% if $JavaScriptEnabled %>
                    <a class="comment-reply-link" href="#{$ReplyForm.FormName}" data-toggle-text="<% _t('CommentsInterface_singlecomment_ss.CANCEL_REPLY','CANCEL_REPLY') %>"><% _t('CommentsInterface_singlecomment_ss.REPLY_TO','Reply to') %>&nbsp;$AuthorName.XML</a>
                <% else %>
                    <a class="comment-reply-link" href="$Parent.Link?replyTo=$ID#Form_ReplyForm_$ID">$ID <% _t('CommentsInterface_singlecomment_ss.REPLY_TO','Reply to') %>&nbsp;$AuthorName.XML</a>
                <% end_if %>
			<% end_if %>
		</div>
	<% end_if %>

<% include CommentReplies %>
<% end_if %>
