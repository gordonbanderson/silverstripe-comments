<div class="comment-reply-form-holder"<% if $JavaScriptEnabled %>style="display:none;"<% end_if %>>
    <% if $JavaScriptEnabled %>$ReplyForm<% else %>
    <%-- non ajax, only show if replyTo is set --%>
    <% if $ShowReplyToForm %>$ReplyForm<% end_if %>
    <% end_if %>
</div>
