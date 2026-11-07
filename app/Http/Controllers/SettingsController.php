<?php

namespace App\Http\Controllers;

use App\Models\OrganizationSetting;
use App\Models\UserPreference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $preferences = UserPreference::firstOrCreate(
            ['user_id' => $user->id],
            $this->defaultPreferences()
        );
        $organization = OrganizationSetting::first() ?? new OrganizationSetting($this->defaultOrganization());

        return view('settings.index', [
            'user' => $user,
            'preferences' => $preferences,
            'organization' => $organization,
            'section' => $request->string('section')->toString() ?: 'profile',
            'timezones' => \DateTimeZone::listIdentifiers(),
        ]);
    }

    public function profile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($request->user())],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($request->user()->profile_photo) {
                Storage::disk('public')->delete($request->user()->profile_photo);
            }
            $data['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $request->user()->update($data);

        return $this->success('profile', 'Profile settings saved.');
    }

    public function password(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'string', 'min:8'],
        ]);

        $request->user()->update(['password' => Hash::make($data['password'])]);

        return $this->success('security', 'Password changed successfully.');
    }

    public function organization(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'organization_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:1000'],
            'organization_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $organization = OrganizationSetting::first() ?? new OrganizationSetting();

        if ($request->hasFile('organization_logo')) {
            if ($organization->logo_path) {
                Storage::disk('public')->delete($organization->logo_path);
            }
            $data['logo_path'] = $request->file('organization_logo')->store('organization', 'public');
        }
        unset($data['organization_logo']);
        $organization->fill($data)->save();

        return $this->success('organization', 'Organization details saved.');
    }

    public function preferences(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'theme' => ['required', Rule::in(['light', 'dark'])],
            'accent_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'sidebar_collapsed' => ['nullable', 'boolean'],
            'email_notifications' => ['nullable', 'boolean'],
            'warranty_alerts' => ['nullable', 'boolean'],
            'maintenance_reminders' => ['nullable', 'boolean'],
            'assignment_notifications' => ['nullable', 'boolean'],
            'return_notifications' => ['nullable', 'boolean'],
        ]);
        $preferences = UserPreference::firstOrCreate(['user_id' => $request->user()->id]);
        $preferences->update(array_merge($data, [
            'sidebar_collapsed' => $request->boolean('sidebar_collapsed'),
            'email_notifications' => $request->boolean('email_notifications'),
            'warranty_alerts' => $request->boolean('warranty_alerts'),
            'maintenance_reminders' => $request->boolean('maintenance_reminders'),
            'assignment_notifications' => $request->boolean('assignment_notifications'),
            'return_notifications' => $request->boolean('return_notifications'),
        ]));

        return $this->success('appearance', 'Notification and appearance preferences saved.');
    }

    public function system(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date_format' => ['required', Rule::in(['Y-m-d', 'd/m/Y', 'm/d/Y'])],
            'timezone' => ['required', Rule::in(\DateTimeZone::listIdentifiers())],
            'currency' => ['required', 'string', 'size:3', 'alpha'],
            'asset_tag_prefix' => ['required', 'string', 'max:20', 'regex:/^[A-Z0-9-]+$/'],
        ]);
        $organization = OrganizationSetting::first() ?? new OrganizationSetting();
        $organization->fill($data)->save();

        return $this->success('system', 'System settings saved.');
    }

    private function success(string $section, string $message): RedirectResponse
    {
        return redirect()->route('settings.index', ['section' => $section])->with('success', $message);
    }

    private function defaultPreferences(): array
    {
        return [
            'email_notifications' => true,
            'warranty_alerts' => true,
            'maintenance_reminders' => true,
            'assignment_notifications' => true,
            'return_notifications' => true,
            'theme' => 'light',
            'accent_color' => '#536df0',
            'sidebar_collapsed' => false,
        ];
    }

    private function defaultOrganization(): array
    {
        return [
            'organization_name' => 'AssetFlow',
            'date_format' => 'Y-m-d',
            'timezone' => config('app.timezone'),
            'currency' => 'USD',
            'asset_tag_prefix' => 'AST-',
        ];
    }
}
