==========================
 DotsUnitedDocCheckBundle
==========================

Integrate authentication via `DocCheck <http://www.doccheck.com/info_dc_password/>`_ into your Symfony2 application.

Installation
============

1. Add this bundle to your project as a Git submodule::

	    $ git submodule add git://github.com/DotsUnited/DotsUnitedDocCheckBundle.git vendor/bundles/DotsUnited/DocCheckBundle

2. Add the ``DotsUnited`` namespace to your autoloader::
	
    // app/autoload.php
    $loader->registerNamespaces(array(
        'DotsUnited' => __DIR__.'/../vendor/bundles',
        // your other namespaces
    );

3. Add this bundle to your application's kernel::

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new DotsUnited\DocCheckBundle\DotsUnitedDocCheckBundle(),
            // ...
        );
    }

4. Add the following routes to your application::

    # app/config/routing.yml
    _security_login:
        pattern:  /login
    _security_check:
        pattern:  /login_check
    _security_logout:
        pattern:  /logout

    # app/config/routing.xml
    <route id="_security_login" pattern="/login">
        <default key="_controller">Security:Security:login</default>
    </route>
    <route id="_security_check" pattern="/login_check" />
    <route id="_security_logout" pattern="/logout" />

5. Create a login controller::

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;

    class SecurityController extends Controller
    {
        public function loginAction()
        {
            return $this->render('Security:Security:login.html.twig');
        }
    }

6. And the corresponding template might look like this::

    <h1>Login</h1>
    {{ doccheck_login_form({'template': 'xl_red'}) }}

7. Configure the dots_united_doc_check service in your config::

    # app/config/config.yml
    dots_united_doc_check:
        login_id: 123456879

    # app/config/config.xml
    <dots_united_doc_check
         login_id="123456879"
    />

8. Configure the security component::

    # app/config/config.yml
    security:
        factories:
            - "%kernel.root_dir%/../vendor/bundles/DotsUnited/DocCheckBundle/Resources/config/security_factories.xml"

    firewalls:
        public:
            pattern:   ^/.*
            dotsunited_doccheck:
                roles: [ROLE_DOCCHECK]
            anonymous: true
    access_control:
        - { path: ^/doccheck/, role: [ROLE_DOCCHECK] }
        - { path: ^/.*,        role: [IS_AUTHENTICATED_ANONYMOUSLY] }

Include the login form in your templates
========================================

Inside a php template::

    <?php echo $view['doccheck']->loginForm(array('template' => 'xl_red', 'special_parameters' => 'param1=value1&param2=value2')) ?>

Inside a twig template::

    {{ doccheck_login_form({'template': 'xl_red', 'special_parameters': 'param1=value1&param2=value2'}) }}
