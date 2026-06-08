<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\BasedController;

/**
 * Controller ERP untuk modul Audit PCN.
 *
 * Extends BasedController yang sudah menyediakan:
 * - $this->service (service name dari config/service-api.yaml)
 * - $this->getHeaders() (header ERP: X-ERP-Payload, X-ERP-Signature, X-ERP-Domain)
 * - $this->get($url, $headers), $this->post($url, $data, $headers)
 *
 * Setiap method merender blade view, dan JavaScript di view
 * akan memanggil API Audit PCN via Axios dengan header ERP.
 */
class AuditController extends BasedController
{
    protected string $service = 'audit';

    public function dashboard()
    {
        $headers = $this->getHeaders();
        return view('audit.dashboard', compact('headers'));
    }

    public function perencanaan()
    {
        $headers = $this->getHeaders();
        return view('audit.perencanaan', compact('headers'));
    }

    public function pkpt()
    {
        $headers = $this->getHeaders();
        return view('audit.pkpt', compact('headers'));
    }

    public function pka()
    {
        $headers = $this->getHeaders();
        return view('audit.pka', compact('headers'));
    }

    public function walkthrough()
    {
        $headers = $this->getHeaders();
        return view('audit.walkthrough', compact('headers'));
    }

    public function todBpm()
    {
        $headers = $this->getHeaders();
        return view('audit.tod-bpm', compact('headers'));
    }

    public function toe()
    {
        $headers = $this->getHeaders();
        return view('audit.toe', compact('headers'));
    }

    public function entryMeeting()
    {
        $headers = $this->getHeaders();
        return view('audit.entry-meeting', compact('headers'));
    }

    public function exitMeeting()
    {
        $headers = $this->getHeaders();
        return view('audit.exit-meeting', compact('headers'));
    }

    public function pelaporan()
    {
        $headers = $this->getHeaders();
        return view('audit.pelaporan', compact('headers'));
    }

    public function penutupLha()
    {
        $headers = $this->getHeaders();
        return view('audit.penutup-lha', compact('headers'));
    }

    public function pemantauan()
    {
        $headers = $this->getHeaders();
        return view('audit.pemantauan', compact('headers'));
    }

    public function monitoring()
    {
        $headers = $this->getHeaders();
        return view('audit.monitoring', compact('headers'));
    }

    public function persetujuan()
    {
        $headers = $this->getHeaders();
        return view('audit.persetujuan', compact('headers'));
    }
}
