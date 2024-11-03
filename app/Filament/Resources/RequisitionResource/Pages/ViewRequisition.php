<?php

namespace App\Filament\Resources\RequisitionResource\Pages;

use Filament\Actions;
use App\Models\Requisition;
use Filament\Pages\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\ActionSize;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\ButtonAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\RichEditor;
use App\Filament\Resources\RequisitionResource;
use Filament\Actions\EditAction;
use Filament\Forms\Concerns\InteractsWithForms;

class ViewRequisition extends ViewRecord
{
    
    protected static string $resource = RequisitionResource::class;

    // Add custom view if necessary
    protected static string $view = 'filament.resources.requisitions.view';

    public string $tab = 'activities';
    public $selectedCommentId;
    public $comment = '';
    public function mount($record): void
    {
        parent::mount($record);
        
        $this->form = $this->makeForm()
            ->schema($this->getFormSchema())
            ->model($this->record)
            ->statePath('commentForm');
    }
    public function getFormSchema(): array
    {
        return [
            RichEditor::make('comment') // Add a field name for binding
                ->statePath('comment')
                ->placeholder('Type a new comment') // Set the placeholder text here
                ->required(),
        ];
    }
    public function selectTab(string $tab): void
    {
        $this->tab = $tab;
    }

    
    protected function getActions(): array
    {
        return [
            EditAction::make('Edit'),
            \Filament\Actions\Action::make('download')
                    ->icon('heroicon-o-document')
                   
                    
                    ->url(fn (Requisition $record): string => route('requisition.pdf', ['requisition' => $record->id]))
                    ->openUrlInNewTab(),
            
            
        ];
    }
}
