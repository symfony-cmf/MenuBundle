# Symfony Cmf Menu Bundle

This is part of the Symfony Cmf: <https://github.com/symfony-cmf/symfony-cmf>

## Configuration

There are some items you can configure:

- menu_basepath:
    default: /cms/menu - the path for the menus in the content repository
- document_manager_name:
    default: default - the name of the document manager
- menu_document_class:
    default: null - the name of the class of the menu documents
- content_url_generator:
    default: router
- content_key:
    default: null
- route_name:
    default: null
- use_sonata_admin:
    default: auto - set this to false if you have sonata admin in your project
        but do not want to use the provided admin service for menu items
- content_basepath:
    default: taken from the core bundle or /cms/content - used for the menu admin

## Links

- GitHub: <https://github.com/symfony-cmf/symfony-cmf>
- Sandbox: <https://github.com/symfony-cmf/cmf-sandbox>
- Web: <http://cmf.symfony.com/>
- Wiki: <http://github.com/symfony-cmf/symfony-cmf/wiki>
- Issue Tracker: <http://cmf.symfony-project.org/redmine/>
- IRC: irc://freenode/#symfony-cmf
- Users mailing list: <http://groups.google.com/group/symfony-cmf-users>
- Devs mailing list: <http://groups.google.com/group/symfony-cmf-devs>

## Documentation

This bundle extends [KnpMenuBundle](https://github.com/KnpLabs/KnpMenuBundle) in order to work with PHPCR ODM. It can go through a [PHPCR](http://phpcr.github.com/) repository and build the corresponding menu. 

The [CMF website](http://cmf.symfony.com) is a concrete example of code using this bundle. It uses the MenuBundle with a custom menu provider, on top of a SQLite PHPCR repository. 

### Installation

This bundle is best included using Composer.

Edit your project composer file to add a new require for `symfony-cmf/menu-bundle`.

Add this bundle (and its dependencies, if they are not already there) to your application's kernel:

	// application/ApplicationKernel.php
	public function registerBundles()
	{
			return array(
			// ...
			new Doctrine\Bundle\PHPCRBundle\DoctrinePHPCRBundle(),
			new Knp\Bundle\MenuBundle\KnpMenuBundle(),
			new Symfony\Cmf\Bundle\MenuBundle\SymfonyCmfMenuBundle(),
			// ...
		);
	}

### Configuration

Add a mapping to `config.yml`, for the knp_menu and for the CMF menu.

	knp_menu:
		twig: true

	symfony_cmf_menu:
		use_sonata_admin: auto|true|false
		menu_basepath: /cms

If `sonata-project/doctrine-phpcr-admin-bundle` is added to the composer.json require, the MenuBundle can be used inside the SonataAdminBundle. But then, the SonataAdminBundle has to be instantiated in your application's kernel.

By default, `use_sonata_admin` configuration is set to true, if the SonataAdminBundle is available.

### Usage

Adjust your template to load the menu.

	{{ knp_menu_render('simple') }}


If your PHPCR repository stores the nodes under `/cms/simple`, use the `simple` alias as argument of `knp_menu_render`.
