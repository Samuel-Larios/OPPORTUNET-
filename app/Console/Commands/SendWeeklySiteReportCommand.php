<?php

namespace App\Console\Commands;

use App\Mail\WeeklySiteReportMail;
use App\Models\ParametreSite;
use App\Support\WeeklySiteReportBuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendWeeklySiteReportCommand extends Command
{
    protected $signature = 'report:weekly-site {--email=}';

    protected $description = 'Envoie le rapport hebdomadaire de visites et d’activités du site.';

    public function handle(WeeklySiteReportBuilder $builder): int
    {
        $recipient = $this->option('email')
            ?: ParametreSite::query()->where('cle', 'site_email')->value('valeur')
            ?: 'contact@opportunetmondiale.com';

        $report = $builder->build();

        Mail::to($recipient)->send(new WeeklySiteReportMail($report));

        $this->info("Rapport hebdomadaire envoyé à {$recipient}.");

        return self::SUCCESS;
    }
}
