### Tranlations

Tranlations are located in ```app/translations/LANGUAGE.js```.

To tranlate we have a handlebars helper ```t``` that makes user of the the following transaltion library: https://github.com/musterknabe/translate.js

Example:

Having the following tranlations:

```
{
  "messages": {
    "messages": "Messages",
    "message": "Message",
    "back": "Back",
    "sender": "Sender",
    "participant": "Participants",
    "subject": "Subject",
    "created_at": "Created at",
    "no_messages": "There are no messages",
    "write_mail": "Write mail",
    "inbox": "Inbox",
    "sent": "Sent",
    "cancel": "Cancel",
    "send": "Send",
    "recipient": "Recipient",
    "successfull_sent": "The message has been successfull sent",
    "cannot_send_to_youself": "You cannot send a message to yourself",
    "message_too_short": "The message is too short",
    "subject_too_short": "The subject is too short",
    "limit_exceded": "You can't post more messages, limit exceced",
    "no_recipient_specified": "No recipient specified or the specified recipient doesn't exist",
    "delete_thread_title": "Delete thread",
    "delete_thread_message": "Are you sure you want to delete this thread?",
    "delete": "Delete",
    "reply": "Reply",
    "sending_message": "Sending messsage"
  }
}
```

To translate the masses inbox:

```
<span class="bigger-110">{{ t "messages::inbox" }}</span>
```

This searches for the inbox entry in the messages namespace.

There is also an ```trans``` service for marionette (App.reqres) to translate the messages. You use it like this:
 
 ```App.request('trans', 'message::inbox');```
 