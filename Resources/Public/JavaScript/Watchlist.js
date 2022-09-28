(function () {

    // From: https://plainjs.com/javascript/utilities/set-cookie-get-cookie-and-delete-cookie-5/
    const cookie = {
        name: 'watchlist',
        days: 7,
        get: function() {
            let v = document.cookie.match('(^|;) ?' + cookie.name + '=([^;]*)(;|$)');
            return v ? v[2] : null;
        },
        set: function(value) {
            let d = new Date;
            d.setTime(d.getTime() + 24 * 60 * 60 * 1000 * cookie.days);
            document.cookie = cookie.name + "=" + value + ";path=/;expires=" + d.toGMTString();
        },
        delete: function() {
            let d = new Date;
            d.setTime(d.getTime() -1);
            document.cookie = cookie.name + "=;path=/;expires=" + d.toGMTString();
        }
    };

    const watchlist = {
        get: function() {
            const cookieValue = cookie.get();
            if (cookieValue === null) {
                return [];
            }

            return cookieValue.split(',');
        },
        save: function(items) {
            var cookieValue = items.join(',');

            if (cookieValue == '') {
                cookie.delete();
            } else {
                cookie.set(items.join(','));
            }
        },
        toggleItem: function(identifier) {
            let items = watchlist.get();
            const position = items.indexOf(identifier);

            if (position === -1) {
                items = items.concat(identifier);
            } else {
                items.splice(position, 1);
            }

            watchlist.save(items);
            watchlist.triggerUpdateEvent(identifier);
        },
        triggerUpdateEvent: function(identifier) {
            const event = new CustomEvent('WatchlistUpdate', {
                detail: {
                    watchlistItem: identifier
                }
            });
            document.dispatchEvent(event);
        }
    };

    const watchlistHtml = {
        selectors: {
            items: '[data-watchlist-item]',
            counter: '[data-watchlist-counter]'
        },
        getItems: function() {
            return document.querySelectorAll(watchlistHtml.selectors.items);
        },
        getCounters: function() {
            return document.querySelectorAll(watchlistHtml.selectors.counter);
        },
        update: function() {
            watchlistHtml.updateCounter();
            watchlistHtml.updateItems();
        },
        updateCounter: function() {
            const count = watchlist.get().length;
            watchlistHtml.getCounters().forEach(function (element) {
                element.innerText = count;
            });
        },
        updateItems: function() {
            const items = watchlist.get();
            watchlistHtml.getItems().forEach(function (element) {
                if (items.indexOf(element.dataset.watchlistItem) === -1) {
                    element.classList.add('watchlist-inactive');
                    element.classList.remove('watchlist-active');
                    return;
                }

                element.classList.remove('watchlist-inactive');
                element.classList.add('watchlist-active');
            });
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        watchlistHtml.update();

        watchlistHtml.getItems().forEach(function (element) {
            element.addEventListener('click', function(event) {
                watchlist.toggleItem(event.currentTarget.dataset.watchlistItem);
            });
        });
    });
    document.addEventListener('WatchlistUpdate', function(event) {
        watchlistHtml.update();
    });
})();
