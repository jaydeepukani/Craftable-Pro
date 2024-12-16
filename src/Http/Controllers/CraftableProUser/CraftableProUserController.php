<?php

namespace Brackets\CraftablePro\Http\Controllers\CraftableProUser;

use Brackets\CraftablePro\Http\Controllers\Controller;
use Brackets\CraftablePro\Http\Requests\CraftableProUser\BulkDestroyCraftableProUserRequest;
use Brackets\CraftablePro\Http\Requests\CraftableProUser\DestroyCraftableProUserRequest;
use Brackets\CraftablePro\Http\Requests\CraftableProUser\ImpersonalLoginCraftableProUserRequest;
use Brackets\CraftablePro\Http\Requests\CraftableProUser\IndexCraftableProUserRequest;
use Brackets\CraftablePro\Http\Requests\CraftableProUser\StoreCraftableProUserRequest;
use Brackets\CraftablePro\Http\Requests\CraftableProUser\UpdateCraftableProUserRequest;
use Brackets\CraftablePro\Models\BaseCraftableProUser;
use Brackets\CraftablePro\Queries\Filters\FuzzyFilter;
use Brackets\CraftablePro\Queries\Sorts\SortNullsLast;
use Brackets\CraftablePro\Settings\GeneralSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Fortify;
use Spatie\Permission\Models\Role;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class CraftableProUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index(IndexCraftableProUserRequest $request): Response | JsonResponse
    {
        $craftableProUsersQuery = QueryBuilder::for(config('craftable-pro.craftable_pro_user_model'))
            ->allowedFilters([
                AllowedFilter::custom('search', new FuzzyFilter(
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'email_verified_at'
                )),
                AllowedFilter::callback('role', fn (Builder $query, $value) => $query->role($value)),
                AllowedFilter::callback('status', function (Builder $query, $value) {
                    if ($value === "pending") {
                        return $query->whereNull("email_verified_at");
                    } else {
                        return $query->whereActive($value)->whereNotNull("email_verified_at");
                    }
                }),
                AllowedFilter::callback('two_factor_auth_enabled', function (Builder $query, $value) {
                    if ($value === true) {
                        return $query->whereNotNull('two_factor_secret')
                            ->when(Fortify::confirmsTwoFactorAuthentication(), function ($query) {
                                $query->whereNotNull('two_factor_confirmed_at');
                            });
                    } else {
                        return $query->where(function ($query) {
                            $query->whereNull('two_factor_secret')
                                ->when(Fortify::confirmsTwoFactorAuthentication(), function ($query) {
                                    $query->orWhereNull('two_factor_confirmed_at');
                                });
                        });
                    }
                }),
            ])
            ->defaultSort('id')
            ->allowedSorts(['id', 'first_name', 'email', 'email_verified_at', AllowedSort::custom('last_active_at', new SortNullsLast())]);

        if ($request->wantsJson() && $request->get('bulk_select_all')) {
            return response()->json($craftableProUsersQuery->select(['id'])->pluck('id'));
        }

        $craftableProUsers = $craftableProUsersQuery
            ->with('roles')
            ->select(['id', 'first_name', 'last_name', 'email', 'email_verified_at', 'active', 'last_active_at', 'two_factor_secret', 'two_factor_confirmed_at'])
            ->paginate(request()->get('per_page'))
            ->withQueryString();

        $roles = Role::all();

        return Inertia::render('CraftableProUser/Index', [
            'craftableProUsers' => $craftableProUsers,
            'filterOptions' => [
                'roles' => $roles->map->only(['id', 'name']),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create(): Response
    {
        $this->authorize('craftable-pro.craftable-pro-user.create');
        $roles = Role::all();

        return Inertia::render('CraftableProUser/Create', [
            'locales' => app(GeneralSettings::class)->available_locales,
            'defaultLocale' => app(GeneralSettings::class)->default_locale,
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCraftableProUserRequest $request
     * @return RedirectResponse
     */
    public function store(StoreCraftableProUserRequest $request)
    {
        $validated = $request->validated();

        $craftableProUser = config('craftable-pro.craftable_pro_user_model')::create($validated);

        $craftableProUser->roles()->sync([$request->input('role_id')]);

        return redirect()->route('craftable-pro.craftable-pro-users.index')->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Display the specified resource.
     *
     * @param BaseCraftableProUser $craftableProUser
     * @return Response
     */
    public function show(BaseCraftableProUser $craftableProUser)
    {
        $this->authorize('craftable-pro.craftable-pro-user.show', $craftableProUser);

        return Inertia::render('CraftableProUser/Show', [
            'craftableProUser' => $craftableProUser,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param BaseCraftableProUser $craftableProUser
     * @return Response
     */
    public function edit(BaseCraftableProUser $craftableProUser)
    {
        $this->authorize('craftable-pro.craftable-pro-user.edit', $craftableProUser);

        $craftableProUser->load('roles');

        $roles = Role::all();

        return Inertia::render('CraftableProUser/Edit', [
            'craftableProUser' => $craftableProUser,
            'avatar' => $craftableProUser->getMedia('avatar'),
            'locales' => app(GeneralSettings::class)->available_locales,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCraftableProUserRequest $request
     * @param BaseCraftableProUser $craftableProUser
     * @return JsonResponse|RedirectResponse
     */
    public function update(UpdateCraftableProUserRequest $request, BaseCraftableProUser $craftableProUser)
    {
        $validated = $request->validated();

        $craftableProUser->update($validated);

        if ($request->input('role_id')) {
            $craftableProUser->roles()->sync([$request->input('role_id')]);
        }

        if ($request->wantsJson()) {
            return response()->json('success');
        }

        return redirect()->route('craftable-pro.craftable-pro-users.index')->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyCraftableProUserRequest $request
     * @param BaseCraftableProUser $craftableProUser
     * @return RedirectResponse
     */
    public function destroy(DestroyCraftableProUserRequest $request, BaseCraftableProUser $craftableProUser)
    {
        $craftableProUser->delete();

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Resend verification notification for user.
     *
     * @param BaseCraftableProUser $craftableProUser
     * @return RedirectResponse
     */
    public function resendEmailVerificationNotification(BaseCraftableProUser $craftableProUser)
    {
        if (! $craftableProUser->hasVerifiedEmail()) {
            if ($craftableProUser->wasInvited()) {
                // FIXME: refactor mailable class
                CraftableProUserInvitationController::sendInvitation(
                    email: $craftableProUser->email,
                    userFullName: Auth::user()->first_name . " " . Auth::user()->last_name,
                );
            } else {
                $craftableProUser->sendEmailVerificationNotification();
            }
        }

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Bulk destroy users.
     *
     * @param BulkDestroyCraftableProUserRequest $request
     * @return RedirectResponse
     */
    public function bulkDestroy(BulkDestroyCraftableProUserRequest $request)
    {
        DB::transaction(function () use ($request) {
            collect($request->validated()['ids'])
                ->chunk(1000)
                ->each(function ($bulkChunk) {
                    config('craftable-pro.craftable_pro_user_model')::whereIn('id', $bulkChunk)->delete();
                });
        });

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Bulk deactivate users.
     * @param BulkDestroyCraftableProUserRequest $request
     * @return RedirectResponse
     */
    public function bulkDeactivate(BulkDestroyCraftableProUserRequest $request)
    {
        DB::transaction(function () use ($request) {
            collect($request->validated()['ids'])
                ->chunk(1000)
                ->each(function ($bulkChunk) {
                    config('craftable-pro.craftable_pro_user_model')::whereIn('id', $bulkChunk)->update(['active' => false]);
                });
        });

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Bulk activate users.
     * @param BulkDestroyCraftableProUserRequest $request
     * @return RedirectResponse
     */
    public function bulkActivate(BulkDestroyCraftableProUserRequest $request)
    {
        DB::transaction(function () use ($request) {
            collect($request->validated()['ids'])
                ->chunk(1000)
                ->each(function ($bulkChunk) {
                    config('craftable-pro.craftable_pro_user_model')::whereIn('id', $bulkChunk)->update(['active' => true]);
                });
        });

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * @param ImpersonalLoginCraftableProUserRequest $request
     * @param BaseCraftableProUser $craftableProUser
     * @return RedirectResponse
     */
    public function impersonalLogin(ImpersonalLoginCraftableProUserRequest $request, BaseCraftableProUser $craftableProUser)
    {
        Auth::login($craftableProUser);

        return redirect()->route('craftable-pro.home');
    }
}
