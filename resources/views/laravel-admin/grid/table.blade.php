<div class="box">
    @if(isset($title))
    <div class="box-header with-border">
        <h3 class="box-title"> {{ $title }}</h3>
    </div>
    @endif

    @if ( $grid->showTools() || $grid->showExportBtn() || $grid->showCreateBtn() )
    <div class="box-header with-border">
        <div class="pull-right">
            {!! $grid->renderColumnSelector() !!}
            {!! $grid->renderExportButton() !!}
            {!! $grid->renderCreateButton() !!}
        </div>
        @if ( $grid->showTools() )
        <div class="pull-left">
            {!! $grid->renderHeaderTools() !!}
        </div>
        @endif
    </div>
    @endif

    {!! $grid->renderFilter() !!}

    <!-- {!! $grid->renderHeader() !!} -->
    {{ $grid->renderHeader() }}

    <!-- /.box-header -->
    <div class="box-body no-padding">
        <table class="table table-hover" id="{{ $grid->tableID }}" style=" display: block; overflow-x: scroll; white-space: nowrap; -webkit-overflow-scrolling: touch;">

            <tbody style="width: 100%; display:table;">
                <tr>
                    @foreach($grid->visibleColumns() as $column)
                    <th class="column-{!! $column->getName() !!}">{{$column->getLabel()}}{!! $column->renderHeader() !!}</th>
                    @endforeach
                </tr>

                @foreach($grid->rows() as $row)
                <tr {!! $row->getRowAttributes() !!}>
                    @foreach($grid->visibleColumnNames() as $name)
                    <td {!! $row->getColumnAttributes($name) !!} class="column-{!! $name !!}">
                        {!! $row->column($name) !!}
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>

            {!! $grid->renderTotalRow() !!}

        </table>

    </div>

    {!! $grid->renderFooter() !!}

    <div class="box-footer clearfix">
        {!! $grid->paginator() !!}
    </div>
    <!-- /.box-body -->
</div>
