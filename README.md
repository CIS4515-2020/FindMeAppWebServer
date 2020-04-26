# FindMeAppWebServer
website + db api for findme app

## Supported API requests
** API only supports **POST** requests! **

** All bolded parameters are required **

** Responses are **UTF-8 JSON encoded** strings **

root api url: **_findmeapp.tech_**

### User
- */register-user*
  - **username**
  - **password**
  - **email**
  - fname
  - lname

- */login*
  - **username**
  - **password**

### Item
- /item/get
  - **item_id**

- /item/list
  - **user_id**
  
- /item/add
  - **user_id**
  - **name**
  - description
  
- /item/edit  - **[atleast 1 of the 3 additional parameters is required]**
  - **item_id**
  - name
  - description
  - lost (true,false)
  
- /item/delete
  - **item_id**

### FoundItemMessage
- /found-item/list
  - **item_id**
  
- /found-item/add  - **[either location or message is required]**
  - **item_id**
  - lat
  - lng
  - message

- /found-item/delete
  - **found_item_id**
