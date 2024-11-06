<?php

namespace App\Filament\Resources\RequisitionResource\Pages;

use Filament\Actions;
use App\Models\Requisition;
use Filament\Actions\Action;
use Filament\Actions\EditAction;

use App\Models\RequisitionComment;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\ActionSize;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\ButtonAction;
use Filament\Resources\Pages\ViewRecord;
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

    

    

  
    // }
    public function selectTab(string $tab): void
    {
        $this->tab = $tab;
    }

    
    protected function getActions(): array
    {
        return [
            EditAction::make('Edit'),
            Action::make('createfile')->tooltip('Download Sage Import file')
            ->icon('heroicon-o-table-cells')
            ->label('Download PO Import file for Sage')
            ->url(fn (Requisition $record): string => route('requisition.text', ['requisition' => $record->id]))
            ->openUrlInNewTab(),
            Action::make('download')
                    ->icon('heroicon-o-document')
                    ->url(fn (Requisition $record): string => route('requisition.pdf', ['requisition' => $record->id]))
                    ->openUrlInNewTab(),
            
            
        ];
    }

   
}
