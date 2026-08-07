<?php

namespace App\Http\Controllers;

use App\Http\Requests\webhook\UpdateWebhookSettingRequest;
use App\Models\WebhookSetting;
use App\Services\TenantContext;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class WebhookSettingController extends Controller
{
    use AuthorizesRequests;
    public function updateWebhooks(UpdateWebhookSettingRequest $request)
    {
        $this->authorize('updateWebhook', WebhookSetting::class); // Optional policy check
        // Get validated data
        $data = $request->validated();

        $organizationId = auth()->user()->organization_id ?? app(TenantContext::class)->id();

        WebhookSetting::updateOrCreate(
            $organizationId ? ['organization_id' => $organizationId] : ['id' => 1],
            $data
        );

        return back()->with('success', 'Webhook settings updated successfully!');
    }

}
