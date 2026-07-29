# Introduction

REST API for FiberFlow — FTTH fiber audit platform with AI-powered network analysis, GIS data management, and project lifecycle tracking.

<aside>
    <strong>Base URL</strong>: <code>http://127.0.0.1</code>
</aside>

    ## Authentication

    Most endpoints require a Bearer token obtained via `/api/v1/login`.

    Include the token in the `Authorization` header:
    ```
    Authorization: Bearer {your_token}
    ```

    Tokens are tied to your user account and do not expire. Use the `logout` endpoint to revoke a token.

    <aside>As you scroll, you'll see code examples for working with the API in different programming languages in the dark area to the right (or as part of the content on mobile).
    You can switch the language used with the tabs at the top right (or from the nav menu at the top left on mobile).</aside>

