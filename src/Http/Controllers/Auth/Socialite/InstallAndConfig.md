#### Config socialite
Add into EventServiceProvider: <br>
`\SocialiteProviders\Manager\SocialiteWasCalled::class => [ `<br>
`\SocialiteProviders\Microsoft\MicrosoftExtendSocialite::class.'@handle',`<br>
`\SocialiteProviders\GitHub\GitHubExtendSocialite::class.'@handle',` <br>
`\SocialiteProviders\Google\GoogleExtendSocialite::class.'@handle',` <br>
``
`]`

Create microsoft azure app in : <a href="https://portal.azure.com/#home">Azure portal</a>

Add into .env <br>
`MICROSOFT_CLIENT_ID=` <br> 
`MICROSOFT_CLIENT_SECRET=` <br>
`MICROSOFT_REDIRECT_URI=` <br>

Allow micrsoft login in `config/socialite/microsoft`

#### When install new socalite login into package:

- search provider in <a href=https://socialiteproviders.com/>Socialite providers</a>
- add new Controller into `src/Http/Controllers/Auth/Socialite/`
- update `config/services.php` config
- update `config/auth.php socialite`
- update CraftableProHandleInertia
- add logo and update login page
