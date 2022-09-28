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

The extension provides an controller with actions to add and remove items from watchlist.
Each item can only exist once within the watchlist.

There is also an action to show the current watchlist.

The extension ships with support for items of type ``pages``,
but this is more a demonstration an used for testing.
Projects are way too different and should provide their own items, see "Custom Items".

The extension also provides a JavaScript which will parse data-Attributes of DOM and
attach listener to add elements to the watchlist.

Custom Items
============

A developer needs to implement an ``ItemHandler`` and an class representing the ``Item``.

The used identifier has to be unique throughout the system. The first part is the
item type which is returned by the ItemHandler to be handled, followed by a minus and
the rest of the identifier, leading to: ``<type>-rest-of-identifier``.
Commas should not be used within the identifier.

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

Example
-------

The extension delivers an example implementation for testing purposes, check out:

- ``Tests/Fixtures/WatchlistExample/Classes/Domain/Items/Page/ItemHandler.php``

- ``Tests/Fixtures/WatchlistExample/Classes/Domain/Items/Page/Page.php``

- ``Tests/Fixtures/WatchlistExample/Configuration/Services.yaml``

The example demonstrates how to fetch information from database,
including file references.

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
