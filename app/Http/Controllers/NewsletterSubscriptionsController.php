<?php

namespace App\Http\Controllers;

use App\ComModules\EMPATIA;
use App\ComModules\Notify;
use Datatables;
use Exception;
use Illuminate\Http\Request;
use One;

class NewsletterSubscriptionsController extends Controller
{

    public function index() {
        try {
            $title = trans('privateNewsletterSubscriptions.list_subscriptions');

            return view('private.newsletterSubscriptions.index', compact('title'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["newsletterSubscription.index" => $e->getMessage()]);
        }
    }

    public function getIndexTable(Request $request)
    {
        try {
            $response = EMPATIA::getNewsletterSubscriptions($request);

            $subscriptions = collect($response->subscriptions);
            $recordsTotal = $response->recordsTotal;
            $recordsFiltered = $response->recordsFiltered;

            return Datatables::of($subscriptions)
                ->editColumn('email', function ($subscription) {
                    return "<a href='" . action("NewsletterSubscriptionsController@show",$subscription->newsletter_subscription_key) . "'>" . $subscription->email . "</a>";
                })
                ->editColumn('active', function ($subscription) {
                    if ($subscription->active == '1')
                        return "<span class=\"btn-sent btn btn-flat btn-success btn-xs\"><i class=\"fa fa-check\" aria-hidden=\"true\"></i></span>";
                    else
                        return "<span class=\"btn-sent btn btn-flat btn-danger btn-xs\"><i class=\"fa fa-times\" aria-hidden=\"true\"></i></span>";
                })
                ->addColumn('action', function ($subscription) {
                    return ONE::actionButtons($subscription->newsletter_subscription_key, ['show' => 'NewsletterSubscriptionsController@show']);
                })
                ->with('filtered', $recordsFiltered ?? 0)
                ->skipPaging()
                ->setTotalRecords($recordsTotal ?? 0)
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["newsletterSubscription.getIndexTable" => $e->getMessage()]);
        }
    }

    public function show(Request $request, $newsletterSubscriptionKey)
    {
        try {
            $newsletterSubscription = EMPATIA::getNewsletterSubscription($newsletterSubscriptionKey);
            return view('private.newsletterSubscriptions.newsletterSubscription', compact("newsletterSubscription"));
        } catch(Exception $e) {
            return redirect()->back()->withErrors(["newsletterSubscription.show" => $e->getMessage()]);
        }
    }

    public function exportAsCsv() {
        try {
            // Getting data to export
            $newsletterSubscriptions = EMPATIA::getNewsletterSubscriptionsToExport();

            $header = [
                trans("privateNewsletterSubscriptions.csv_header_email"),
                trans("privateNewsletterSubscriptions.csv_header_is_active"),
                trans("privateNewsletterSubscriptions.csv_header_created_at")
            ];

            $newsletterSubscriptions = array_merge([$header],$newsletterSubscriptions);

            header("Content-type: text/csv");
            header("Content-disposition: attachment; filename = newsletterSubscriptions.csv");

            foreach ($newsletterSubscriptions as $line) {
                $line = collect($line)->toArray();
                echo utf8_decode(implode(',',$line)) . "\n";
            }
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["newsletterSubscription.exportAsCsv" => $e->getMessage()]);
        }
    }
}
