This template is intended as a scaffolding to create a new Craft CMS project. It's intended to serve as the backend for use as a headless resource (either with GraphQL or RESTful API).

I will expand with descriptions as time permits (I apologize).

## How to use with Nuxt

You would need to modify the "publicPath" property in your nuxt.config.js file to something like this:

```
  build: {
    
    "publicPath": "/app/_nuxt"

  }
```

In a Unix environment, you can utilize a hard link (symbolic does not work in my use case) to an html file that your templates reference in Twig, linking to the distributed result after you generate it statically.

### Example Script

You may need to move the distributed folder underneath public. For example, to have the nuxt application under "app" in public, you could use the following, replacing BASE_DIR with the folder you are using.

```
#!/bin/bash

: ${PROJECT_NAME:="hrotti"}

: ${BASE_DIR:="/srv/apps/$PROJECT_NAME"}

: ${OPS_DIR:="${BASE_DIR}/ops"}
: ${APP_DIR:="${BASE_DIR}/app"}

: ${BACKEND_DIR:="${APP_DIR}/backend"}
: ${FRONTEND_DIR:="${APP_DIR}/frontend"}

# ----------------------------------------

cd $FRONTEND_DIR

git pull

npm ci
npm run generate

mv ${BASE_DIR}/app/frontend/dist/app/_nuxt ${BASE_DIR}/app/frontend/dist/_nuxt
rm -rf ${BASE_DIR}/app/frontend/dist/app

sudo rm -rf "${BASE_DIR}/app/backend/src/public/app"

cp -R "${BASE_DIR}/app/frontend/dist/" "${BASE_DIR}/app/backend/src/public/app"

sudo rm -f "${BASE_DIR}/app/backend/src/craftcms/templates/index.html"
ln "${BASE_DIR}/app/backend/src/public/app/index.html" "${BASE_DIR}/app/backend/src/craftcms/templates/index.html"
```