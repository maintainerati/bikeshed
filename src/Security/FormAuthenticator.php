<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Security;

use Maintainerati\Bikeshed\Repository\AttendeeRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class FormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    /** @var AttendeeRepository */
    private $repository;
    /** @var RouterInterface */
    private $router;
    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;
    /** @var SessionInterface */
    private $session;

    public function __construct(
        AttendeeRepository $repository,
        RouterInterface $router,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder,
        SessionInterface $session
    ) {
        $this->repository = $repository;
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->session = $session;
    }

    public function supports(Request $request)
    {
        $route = $request->attributes->get('_route');
        $isPost = $request->isMethod(Request::METHOD_POST);

        return $route === 'bikeshed_login' && $isPost;
    }

    public function getCredentials(Request $request)
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $token = $request->request->get(Csrf::PARAMETER);
        if (!$email || !$password || !$token) {
            throw new AccessDeniedException();
        }
        $credentials = [
            'email' => $email,
            'password' => $password,
            'csrf_token' => $token,
        ];
        $this->session->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken(Csrf::TOKEN_ID, $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->repository->findOneBy(['email' => $credentials['email']]);
        if ($user) {
            return $user;
        }

        // fail authentication with a custom error
        throw new CustomUserMessageAuthenticationException('Email could not be found.');
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($this->session, $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('bikeshed_homepage'));
    }

    protected function getLoginUrl()
    {
        return $this->router->generate('bikeshed_login');
    }
}
