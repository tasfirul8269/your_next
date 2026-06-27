<?php

namespace Frooxi\Shop\Http\Controllers\Customer\Account;

use Frooxi\Shop\Http\Controllers\Controller;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * Displays the listing resources if the customer having items in wishlist.
     *
     * @return View
     */
    public function index()
    {
        if (! core()->getConfigData('customer.settings.wishlist.wishlist_option')) {
            abort(404);
        }

        return view('shop::customers.account.wishlist.index');
    }
}
