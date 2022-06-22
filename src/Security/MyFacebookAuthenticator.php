<?php
namespace App\Security;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Pimcore\Bundle\AdminBundle\Security\User\User;
use Pimcore\Cache\Runtime;
use Pimcore\Event\Admin\Login\LoginCredentialsEvent;
use Pimcore\Event\Admin\Login\LoginRedirectEvent;
use Pimcore\Event\AdminEvents;
use Pimcore\Model\User\Listing;
use Pimcore\Tool\Admin;
use Pimcore\Tool\Authentication;
use Pimcore\Tool\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Pimcore\Model\User as UserModel;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class MyFacebookAuthenticator extends SocialAuthenticator
{

    private $clientRegistry;
    private $em;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, RouterInterface $router,
    TranslatorInterface $translator,
    EventDispatcherInterface $dispatcher,

    )
    {
        $this->clientRegistry = $clientRegistry;
        $this->router = $router;
        $this->translator = $translator;
        $this->dispatcher = $dispatcher;


    }

    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'connect_facebook_check';
    }

    public function getCredentials(Request $request)
    {
        throw new AuthenticationException('Missing username or token');

            $credential =  $this->fetchAccessToken($this->getFacebookClient());
            /** @var FacebookUser $facebookUser */

            $event = new LoginCredentialsEvent($request, ['accessToken'=>$credential]);
            $this->dispatcher->dispatch($event, AdminEvents::LOGIN_CREDENTIALS);

            return $event->getCredentials();

        }

        public function getUser($credentials, UserProviderInterface $userProvider)
        {

            if (!is_array($credentials)) {
                throw new AuthenticationException('Invalid 1 credentials');

            }

            /** @var \League\OAuth2\Client\Provider\AzureResourceOwner **/
            $facebookUser = $this->getFacebookClient()
                ->fetchUserFromToken($credentials['accessToken']);
            $email = $facebookUser->claim('email');

            $userList = new Listing();

            $userList->setCondition("email = ?", [$email]);
            $pimcoreUser = $userList->load()[0];


            $user = new User($pimcoreUser);

            Session::useSession(function (AttributeBagInterface $adminSession) use ($user) {
                Session::regenerateId();
                $adminSession->set('user', $user);
            });
        return $user;
    }

    /**
     * @return OAuth2ClientInterface
     */
    private function getFacebookClient()
    {
        return $this->clientRegistry
            // "facebook_main" is the key used in config/packages/knpu_oauth2_client.yaml
            ->getClient('facebook_main');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
           /** @var UserModel $user */
           $user = $token->getUser()->getUser();
           // set user language
           $request->setLocale($user->getLanguage());
           $this->translator->setLocale($user->getLanguage());

           // set user on runtime cache for legacy compatibility
           Runtime::set('pimcore_admin_user', $user);

           if ($user->isAdmin()) {
               if (Admin::isMaintenanceModeScheduledForLogin()) {
                   Admin::activateMaintenanceMode(Session::getSessionId());
                   Admin::unscheduleMaintenanceModeOnLogin();
               }
           }

           // as we authenticate statelessly (short lived sessions) the authentication is called for
           // every request. therefore we only redirect if we're on the login page


           $url = null;
           if ($request->get('deeplink') && $request->get('deeplink') !== 'true') {
               $url = $this->router->generate('pimcore_admin_login_deeplink');
               $url .= '?' . $request->get('deeplink');
           } else {
               $url = $this->router->generate('pimcore_admin_index', [
                   '_dc' => time(),
                   'perspective' => strip_tags($request->get('perspective')),
               ]);
           }

           if ($url) {
               $response = new RedirectResponse($url);
            //    $response->headers->setCookie(new Cookie('pimcore_admin_sid', true, 0, '/', null, false, true));

               return $response;
           }

           return null;

    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {

        $url = $this->router->generate('pimcore_admin_login', [
            'auth_failed' => 'true',
        ]);

        return new RedirectResponse($url);
    }

        /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {

        $event = new LoginRedirectEvent('pimcore_admin_login', ['perspective' => strip_tags($request->get('perspective', ''))]);
        $this->dispatcher->dispatch($event, AdminEvents::LOGIN_REDIRECT);

        $url = $this->router->generate($event->getRouteName(), $event->getRouteParams());
        return new RedirectResponse($url);
    }

    // ...
}
