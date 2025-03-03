<?php

namespace App\Filament\Resources\RequisitionResource\Pages;

use Filament\Actions;
use App\Models\Requisition;
use Filament\Actions\Action;
use Filament\Actions\EditAction;

use App\Models\RequisitionComment;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\ActionSize;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\ButtonAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use League\CommonMark\Input\MarkdownInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\RequisitionResource;
use Filament\Forms\Concerns\InteractsWithForms;

class ViewRequisition extends ViewRecord implements HasForms
{
    protected static string $resource = RequisitionResource::class;

    // Add custom view if necessary
    protected static string $view = 'filament.resources.requisitions.view';

    public string $tab = 'comments';
    public $selectedCommentId;
    public $comment = '';
    public function getTotalAmount()
    {
        // Assuming the `lineItems` relationship is defined on `ViewRequisition`
        return $this->record->lineItems->sum(function ($item) {
            return $item->cost * $item->qty;
        });
    }

    // }
    public function selectTab(string $tab): void
    {
        $this->tab = $tab;
    }

    protected function getActions(): array
    {
        return [
            EditAction::make('Edit'),
            // Action::make('createfile')
            //     ->tooltip('Download Sage Import file')
            //     ->icon('heroicon-o-document-arrow-down')
            //     ->label('Download PO Import file for Sage')
            //     ->url(
            //         fn(Requisition $record): string => route(
            //             'requisition.text',
            //             ['requisition' => $record->id]
            //         )
            //     )
            //     ->openUrlInNewTab(),

            Action::make('download')
                ->icon('heroicon-o-document')
                ->url(
                    fn(Requisition $record): string => route(
                        'requisition.pdf',
                        ['requisition' => $record->id]
                    )
                )
                ->openUrlInNewTab(),
            Action::make('upload items')
                ->tooltip('Upload csv to add material items')
                ->icon('heroicon-o-arrow-up-tray')
                ->iconButton()
                // ->hidden(fn($record): bool => $record->is_processed)
                ->form([
                    FileUpload::make('upload_csv')
                        ->required()
                        ->acceptedFileTypes(['text/csv'])
                        ->label(
                            'Csv file must have the headers "item_code", "description", "qty" - .xlsx is not supported. Uploading items will replace any existing items - proceed with caution.'
                        ),
                ])
                ->action(function (array $data, Requisition $record): void {
                    // Call the service method for processing the CSV upload
                    $success = RequisitionResource::processCsvUpload(
                        $data,
                        $record
                    );

                    if ($success) {
                        // Handle success logic, e.g., show a success message
                    } else {
                        // Handle failure, e.g., log or show an error message
                    }
                })
                ->visible(fn() => Auth::user()->can('upload_requisition')),
        ];
    }
}
