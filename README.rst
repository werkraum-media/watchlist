=========================
TYPO3 Extension Watchlist
=========================

Adds the feature of a watchlist to the frontend of TYPO3 CMS.

Users are able to add "items" to the watchlist.
They are also able to remove things from watchlist and show the current watchlist.

Items can be anything. The extension uses interfaces and developers are able to
connect whatever they want to be a thing that can be added to the watchlist.

Feature set
===========

The extension provides an controller with actions to add and remove items from watchlists.
Each item can only exist once within each watchlist.

There is also an action to show a watchlist.

The extension has the concept of multiple watchlists if necessary,
provide the watchlist identifier, it will fallback to "default".
But the only storage implementation of cookie does not handle that concept yet.

The extension does not ship with any out of the box support.
Projects are way too different and should provide their own items, see "Custom Items".
But an example is provided, see Example section below.

The extension also provides a JavaScript which will parse data-Attributes of DOM and
attach listener to add elements to the watchlist.

Allows to render the items of watchlist within an EXT:form form.

Concept
=======

The extension only provides the functionality, no concrete project specific implementation.
It is up to the developer and integrator of the project to provide necessary pieces, see "Custom Items".
The extension always only uses identifier of each item and does not know anything else about items.

Custom Items
============

A developer needs to implement an ``ItemHandler`` and an class representing the ``Item``.
``FormElementAwareItem`` can optionally be implemented as well, in order to make use of items within an EXT:form form.

The used identifier has to be unique throughout the system.
The first part is the item type which is returned by the ItemHandler to be handled,
followed by a minus and the rest of the identifier, leading to: ``<type>-rest-of-identifier``.

Commas should not be used within the identifier.
The identifiers will be stored in as csv within a cookie.

``ItemHandler``
---------------

The class needs to implement the ``WerkraumMedia\Watchlist\Domain\ItemHandlerInterface``.
The purpose is to convert an identifier of that item to an actual instance of that item.
The Handler needs to be registered via Symfony Tags, e.g. via ``Services.yaml``:

.. code:: yaml

     WerkraumMedia\WatchlistExample\PageItem\ItemHandler:
       tags: ['watchlist.itemHandler']

``Item``
--------

The class needs to implement the ``WerkraumMedia\Watchlist\Domain\Model\Item``.

``FormElementAwareItem``
------------------------

The class can implement the ``WerkraumMedia\Watchlist\Domain\Model\FormElementAwareItem``.

The methods are used by the default EXT:form integration.
It is up to the developer + integrator to not use this interface and alter the rendering.

Example
-------

The extension delivers an example implementation for testing purposes, check out:

- ``Tests/Fixtures/WatchlistExample/Classes/Domain/Items/Page/ItemHandler.php``

- ``Tests/Fixtures/WatchlistExample/Classes/Domain/Items/Page/Page.php``
  This implements both ``Item`` as well as ``FormElementAwareItem``.

- ``Tests/Fixtures/WatchlistExample/Configuration/Services.yaml``

The example demonstrates how to fetch information from database,
including file references.

EXT:form integration
====================

The provided Configuration needs to be loaded via TypoScript.
Use a free identifier:

.. code:: plain

   plugin.tx_form.settings.yamlConfigurations {
       80 = EXT:watchlist/Configuration/Form/Setup.yaml
   }

This will register a new form element type ``Watchlist`` that can be used like this:

.. code:: yaml

   -
     type: Watchlist
     identifier: watchlist-1
     label: 'Watchlist'

A default template is provided which will render all items with checkboxes.

JavaScript
==========

The JavaScript respects two ``data-`` attributes:

``data-watchlist-counter``
   Defines that this element will hold the current number of items on watch list.
   The JavaScript will update the content of the element.

   .. code:: html

      <span data-watchlist-counter class="watchlist-badge-counter"></span>

``data-watchlist-item``
   Defines that this element represents an actual item.
   The attribute needs the identifier of the item as value, e.g.: ``data-watchlist-item="page-1"``

   An EventListener will be added listening for click events on that element.
   Each click will toggle the state of the item on the watch list.

   The JavaScript will add a CSS class to the element, depending on the state of the item.
   ``watchlist-inactive`` will be added in case the item is not on the watchlist.
   ``watchlist-active`` will be added in case the item is on the watchlist.

A custom Event will be triggered whenever an item is added or removed from the watchlist.
The event is triggered on the ``document``.
The event provides the identifier of the item.
An example listener can look like:

.. code:: js

   document.addEventListener('WatchlistUpdate', function(event) {
       console.log(event.detail.watchlistItem);
   });

Example
-------

A concrete example can be found within ``Tests/Fixtures/FrontendRendering.typoscript``.
This example includes the provided JavaScript file as well as some custom CSS and Markup.
The content element is not necessary.

Changelog
=========

v3.0.0
------

* Add Support for TYPO3 v13.4.

* Drop Support for TYPO3 v12.4 and PHP 8.1.

v2.0.0
------

* Add Support for TYPO3 v12.4 and PHP 8.4.

* Drop Support for TYPO3 v11 and PHP versions not supported by TYPO3 v12.

v1.1.0
------

* Allow replacement of cookie implementation.
  The implementation is now extracted into an interface.
  That way it is easier to replace the concrete implementation for cookie handling
  with a custom one.

v1.0.1
------

* Fix broken registration of plugin as content element within TCA.
  We used different cases for ExtensionName and PluginName while configuring and registering a plugin.
  That lead to the issue that the plugin was also registered as list_type aka "Insert Plugin".
  But there was no rendering definition. We manually added the CType registration.

  This is now fixed by properly calling the registerPlugin() method. That way extbase
  can find the CType definition and will add it as CType instead of list_type.

* Fix broken cookie handling.

  * For some reason we used the `/typo3/` path while storing cookies server side.
    But we used `/` in JavaScript.
    That didn't play together and was fixed to always be `/` for now, but it should be configurable in general.
    The fix revealed that the detection of whether to store a cookie was broken, which was fixed within the corresponding service.

  * Furthermore the dates how long the cookie should be stored was different.
    We now always use 7 days.

  * And we now properly encode the value of the cookie,
    in order to prevent issues with special meanings like `;`.
    This was already done on PHP side but not within JS.
