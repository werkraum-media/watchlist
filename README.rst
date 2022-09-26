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

There is also an action to show the current watchlist.

The extension ships with support for items of type ``pages``,
but this is more a demonstration an used for testing.
Projects are way too different and should provide their own items, see "Custom Items".

Custom Items
============

A developer needs to implement an ``ItemHandler`` and an class representing the ``Item``.

``ItemHandler``
---------------

The class needs to implement the ``WerkraumMedia\Watchlist\Domain\ItemHandlerInterface``.
The purpose is to convert an identifier of that item to an actual instance of that item.
The Handler needs to be registered via Symfony Tags, e.g. via ``Services.yaml``:

.. code:: yam;

   services:
     _defaults:
       autowire: true
       autoconfigure: true
       public: false

     WerkraumMedia\Watchlist\:
       resource: '../Classes/*'

     WerkraumMedia\Watchlist\Domain\Items\Page\ItemHandler:
       tags: ['watchlist.itemHandler']

``Item``
--------

The class needs to implement the ``WerkraumMedia\Watchlist\Domain\Model\Item``.
