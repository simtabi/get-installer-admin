<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TenantResource;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantController extends Controller
{
    /**
     * GET /api/v1/tenants/me
     *
     * Returns the authenticated user's tenant. Conforms to the
     * Tenant schema in docs/api/v1.yaml.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user === null) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        $tenantId = $user->tenant_id;
        if (! is_string($tenantId)) {
            abort(Response::HTTP_FORBIDDEN, 'user has no tenant');
        }

        $tenant = Tenant::query()->findOrFail($tenantId);

        TenantResource::withoutWrapping();

        return (new TenantResource($tenant))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }
}
