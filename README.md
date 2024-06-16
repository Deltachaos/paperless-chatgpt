# paperless-chatgpt

Creates a title for all documents on a Paperless NGX server containing a specifc string in the title. Currently supporting german language. If you want to change the language, change the text in `config/title.prompt.txt`.

## Configuration

```sh
export PAPERLESS_SERVER=https://paperless.example.com
export PAPERLESS_KEY=01askk**21378asdkl # From your user profile
export PAPERLESS_SEARCH=Scanned_from_Scanner # The text to search in the documents
export OPENAI_KEY=sess-***** # from your openai profile
export INTERVAL=120 # sleep between checks in seconds. Default 5 minutes
```
