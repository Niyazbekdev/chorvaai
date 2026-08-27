<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Services\BannerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function __construct(private BannerService $bannerService) {}

    /** Reklama yaratish formasi */
    public function create(): View
    {
        return view('banners.create');
    }

    /** Reklama ma'lumotlarini saqlash → to'lovga yo'naltirish */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title'   => ['required', 'string', 'max:100'],
            'contact' => ['required', 'string', 'max:50'],
            'url'     => ['nullable', 'url', 'max:255'],
            'image'   => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $banner = $this->bannerService->store(
            $request->only('title', 'contact', 'url'),
            $request->file('image'),
            $request->user()->id,
        );

        return redirect()->route('payment.select', [
            'type'      => 'banner',
            'banner_id' => $banner->id,
        ]);
    }

    /** Foydalanuvchining bannerlari */
    public function myBanners(Request $request): View
    {
        $banners = Banner::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('banners.my-banners', compact('banners'));
    }

    /** Arxivdagi bannerni qayta aktivlashtirish (to'lovga yo'naltirish) */
    public function reactivate(Banner $banner): RedirectResponse
    {
        if ($banner->user_id !== auth()->id()) {
            abort(403);
        }

        if ($banner->status !== Banner::STATUS_ARCHIVED) {
            return back()->with('error', 'Faqat arxivdagi bannerlarni qayta aktivlashtirish mumkin.');
        }

        $banner->update(['status' => Banner::STATUS_PENDING]);

        return redirect()->route('payment.select', [
            'type'      => 'banner',
            'banner_id' => $banner->id,
        ]);
    }

    /** Bannerni o'chirish */
    public function destroy(Banner $banner): RedirectResponse
    {
        if ($banner->user_id !== auth()->id()) {
            abort(403);
        }

        $this->bannerService->delete($banner);

        return back()->with('success', 'Banner o\'chirildi.');
    }
}
