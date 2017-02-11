<?php



namespace BD\UserBundle\Controller;

use BD\UserBundle\Entity\User;
use BD\UserBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller {
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request) {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@BDUser/security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
        // you'll need to create a route for this URL (but not a controller)
    }

    /**
     * @Route("/signin", name="signin")
     */
    public function signinAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encoder = $this->get('security.password_encoder');
            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->addRole('ROLE_USER');
            $token = $this->generateUniqId();
            $user->setToken($token);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            /*mail*/
            $parameters = [
                "username" => $user->getUsername(),
                "urlUser"  => 'http://localhost/user-bundle/web/app_dev.php/confirmation/'. $token . '/' . $user->getEmail() // token ???
            ];

            $message = \Swift_Message::newInstance()
                ->setSubject('Confirmation')
                ->setFrom('oo@oo.oo')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        '@BDUser/emails/registration-email.html.twig',
                        $parameters
                    ),
                    'text/html'
                )
                ->addPart(
                    $this->renderView(
                        '@BDUser/emails/registration-email.txt.twig',
                        $parameters
                    ),
                    'text/plain'
                )
            ;
            $this->get('mailer')->send($message);
            /*/mail*/
        }

        return $this->render('@BDUser/security/signin.html.twig', [
            'form' => $form->createView()
        ]);
    }

    private function generateUniqId() {
        $result = bin2hex(openssl_random_pseudo_bytes(16));
        return $result;
    }
}
