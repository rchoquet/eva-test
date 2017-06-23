<?php

class TemplateManager
{
    private $appContext;
    private $quoteRepo;
    private $siteRepo;
    private $destinationRepo;

    public function __construct(
        ApplicationContext $appContext,
        QuoteRepository $quoteRepo,
        SiteRepository $siteRepo,
        DestinationRepository $destinationRepo
    ) {
        $this->appContext = $appContext;
        $this->quoteRepo = $quoteRepo;
        $this->siteRepo = $siteRepo;
        $this->destinationRepo = $destinationRepo;
    }

    public function getTemplateComputed(Template $tpl, array $data)
    {
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }

        $replaced = clone($tpl);
        $replaced->subject = $this->computeText($replaced->subject, $data);
        $replaced->content = $this->computeText($replaced->content, $data);

        return $replaced;
    }

    private function computeText($text, array $data)
    {
        $quote = (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;

        if ($quote) {
            $quote = $this->quoteRepo->getById($quote->id);
            $site = $this->siteRepo->getById($quote->siteId);
            $destination = $this->destinationRepo->getById($quote->destinationId);

            $containsSummaryHtml = strpos($text, '[quote:summary_html]');
            $containsSummary = strpos($text, '[quote:summary]');

            if ($containsSummaryHtml !== false || $containsSummary !== false) {
                if ($containsSummaryHtml !== false) {
                    $text = str_replace(
                        '[quote:summary_html]',
                        Quote::renderHtml($quote),
                        $text
                    );
                }
                if ($containsSummary !== false) {
                    $text = str_replace(
                        '[quote:summary]',
                        Quote::renderText($quote),
                        $text
                    );
                }
            }

            (strpos($text, '[quote:destination_name]') !== false) and $text = str_replace('[quote:destination_name]', $destination->countryName, $text);
        }

        if (isset($destination)) {
            $text = str_replace('[quote:destination_link]', $site->url.'/'.$destination->countryName.'/quote/'.$quote->id, $text);
        } else {
            $text = str_replace('[quote:destination_link]', '', $text);
        }

        /*
         * USER
         * [user:*]
         */
        $_user = (isset($data['user']) and ($data['user'] instanceof User)) ? $data['user'] : $this->appContext->getCurrentUser();
        if ($_user) {
            (strpos($text, '[user:first_name]') !== false) and $text = str_replace('[user:first_name]', ucfirst(mb_strtolower($_user->firstname)), $text);
        }

        return $text;
    }
}
