<?php

namespace App\DataTables\Admin;

use App\DataTables\BaseDataTable;
use App\Product;
use App\Project;
use App\CodeType;
use App\SalescategoryType;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Carbon\Carbon;

class ProductsDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($row) {
                $action = '<div class="btn-group dropdown m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
                <ul role="menu" class="dropdown-menu pull-right">
                  <li><a href="' . route('admin.products.edit', [$row->id]). '"><i class="fa fa-pencil" aria-hidden="true"></i> ' . trans('app.edit') . '</a></li>
                    <li><a href="' . route('admin.products.edit', [$row->id, "copy"=>1]). '"><i class="fa fa-copy" aria-hidden="true"></i> ' . trans('app.copy') . '</a></li>
                  <li><a href="javascript:;"  data-user-id="' . $row->id . '"  class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> ' . trans('app.delete') . '</a></li>
                  <li><a href="'. route("admin.products.download", $row->id).'"><i class="fa fa-file-pdf-o"></i> ' . trans('app.download') . '</a></li>';
                
                $action .= ' <li><a href="javascript:" data-user-id="' . $row->id . '" class="pa-rfq" data-toggle="modal" data-target="#productRFQModal"><i class="fa fa-send"></i> RFQ </a></li>';
                

                $action .= '</ul> </div>';

                return $action;
            })
        ->setRowData(['data-id' => function($row) {
                return $row->id;
            }
        ])
            ->editColumn('name', function ($row) {
                $name = '';
                if(isset($row->name) && !empty($row->name)) {
                    $name = ucfirst($row->name);
                }
                return $name;
            })
             ->editColumn('pPicture', function ($row){
                if(!empty($row->picture)) {
                    $pictures = json_decode($row->picture);
                    if($pictures) {
                        $image =  asset('user-uploads/products/'.$row->id.'/'.$pictures[0].'');
                        return  '<img src="' . $image . '" alt="product"  width="50" height="50">';
                    }
                    return '';
                } 
                return '';
            })
//            ->editColumn('locationCode', function ($row) {
//               $item = json_decode($row->item);
//               if(isset($item->locationCode) && !empty($item->locationCode)) {
//                    $locationCode = CodeType::where('location_code', $item->locationCode)->first();
//                    if($locationCode) {
//                        return ucfirst($locationCode->location_name);
//                    }
//                    return '';
//               }
//               return '';
//            })
             ->addColumn('projectName', function ($row) {
                 
                 $return = '';
                 
                  if (!is_null($this->project_id)) {
                      $projectDT = Project::where('id', $this->project_id)->first();
                      if($projectDT) {
                        $return = ucfirst($projectDT->project_name);
                      }
                  } else {
                      // new logic assign multiple projects to products
                    if($row->projects) { 
                         foreach ($row->projects as $project) {
                             $projectDT = Project::where('id', $project->project_id)->first();
                             if($projectDT) {
                                if($return == ''){
                                    $return .= ucfirst($projectDT->project_name);
                                }else {
                                    $return .=', '.ucfirst($projectDT->project_name);
                                }
                            }
                         }
                     }
                      
                  }
                return $return;
                     
                
                 
                 // was single project assign to prouct
//                if(!empty($row->project_id)) {
//                    $project = Project::where('id', $row->project_id)->first();
//                    if($project) {
//                        return ucfirst($project->project_name);
//                    }
//                    return '';
//                }
                
                //return $row->project_id;
            })
//            ->editColumn('quantity', function ($row) {
//               $item = json_decode($row->item);
//                return ucfirst($item->quantity);
//            })
            ->addColumn('locationCode', function ($row) {
               
                $locationCode = '';
                if($row->codes) {
                    foreach ($row->codes as $code) {
                        if($code->code) {
                            if($locationCode == '') {
                                $locationCode = ucfirst($code->code->location_name);
                            } else {
                                $locationCode .=','.ucfirst($code->code->location_name);
                            }
                        }
                    }
                }
               return $locationCode;
            })
            ->addColumn('salesCategory', function ($row) {
               $item = json_decode($row->item);
               if(isset($item->salesCategory) && !empty($item->salesCategory)) {
                    $salesCategory = SalescategoryType::where('salescategory_code', $item->salesCategory)->first();
                    if($salesCategory) {
                        return ucfirst($salesCategory->salescategory_name);
                    }
                    return '';
                    
               }
               return '';
            })
            ->editColumn('manufacturer', function ($row) {
                return ucfirst($row->manufacturer);
            })
            ->editColumn('finish_color', function ($row) {
                return ucfirst($row->finish_color);
            })
            ->editColumn('dimensions', function ($row) {
                return ($row->dimensions);
            })
            
            
            ->editColumn('materials', function ($row) {
                return ($row->materials);
            })
             ->addColumn('vendor', function ($row) {
                 $vendor_name = '';
                if ($row->vendor_id) {
                    if($row->vendor) {
                        $vendor_name =  ucfirst($row->vendor->company_name);
                    }
                }
                return $vendor_name;
            })
             ->editColumn('vendor_description', function ($row) {
                return ($row->vendor_description);
            })
             ->editColumn('notes', function ($row) {
                return ($row->notes);
            })
            ->editColumn('cost_per_unit', function ($row) {
                $cost_per_unit = '';
                if(isset($row->cost_per_unit) && !empty($row->cost_per_unit) && is_numeric($row->cost_per_unit)) {
                $cost_per_unit =  $row->cost_per_unit? number_format($row->cost_per_unit, 2) : '';
                }
                return $cost_per_unit;
            })
            
            ->editColumn('msrp', function ($row) {
                $msrp = '';
                if(isset($row->msrp) && !empty($row->msrp) && is_numeric($row->msrp)) {
                    $msrp =  $row->msrp ? number_format($row->msrp, 2) : '';
                }
                return $msrp ;
            })
            ->editColumn('url', function ($row) {
                $url = '';
                if(isset($row->url) && !empty($row->url)) {
                    $url = $row->url;
                }
                return $url;
            })
            ->editColumn('markup_fix', function ($row) {
                $markup_fix = '';
                if(isset($row->markup_fix) && !empty($row->markup_fix) && is_numeric($row->markup_fix)) {
                    $markup_fix =  $row->markup_fix? number_format($row->markup_fix, 2) : '';
                }
                return $markup_fix;
                
            })
            ->editColumn('markup_per', function ($row) {
                
                $markup_per = '';
                if(isset($row->markup_per) && !empty($row->markup_per) && is_numeric($row->markup_per)) {
                    $markup_per = number_format($row->markup_per, 2);
                }
                return $markup_per;
                
            })
             ->editColumn('default_markup_fix', function ($row) {
                 $default_markup_fix = '';
                 
                 if(isset($row->default_markup_fix) && !empty($row->default_markup_fix) && is_numeric($row->default_markup_fix)) {
                     $default_markup_fix = $row->default_markup_fix ? number_format($row->default_markup_fix, 2) : '';
                 }
                 return $default_markup_fix; 
                
            })
            ->editColumn('sales_tax_fix', function ($row) {
                $sales_tax_fix = '';
                if(isset($row->sales_tax_fix) && !empty($row->sales_tax_fix) && is_numeric($row->sales_tax_fix)) {
                    $sales_tax_fix = $row->sales_tax_fix ? number_format($row->sales_tax_fix, 2) : '';
                }
                return $sales_tax_fix;
            })
            ->editColumn('sales_tax_per', function ($row) {
                $sales_tax_per = '';
                
                if(isset($row->sales_tax_per) && !empty($row->sales_tax_per) && is_numeric($row->sales_tax_per)) {
                    $sales_tax_per =  $row->sales_tax_per ? number_format($row->sales_tax_per, 2) : '';
                }
                return $sales_tax_per;
                
            })
            ->editColumn('freight', function ($row) {
                
                $freight = '';
                if(isset($row->freight) && !empty($row->freight) && is_numeric($row->freight)) {
                    $freight =  $row->freight ? number_format($row->freight, 2) : '';
                }
                return $freight;
                
            })
            ->editColumn('total_sale', function ($row) {
                $total_sale = '';
                if(isset($row->total_sale) && !empty($row->total_sale) && is_numeric($row->total_sale)) {
                    $total_sale = $row->total_sale? number_format($row->total_sale, 2) : '';
                }
                return $total_sale;
                
            })
            ->editColumn('acknowledgement', function ($row) {
                return ($row->acknowledgement);
            })
            ->editColumn('received_by', function ($row) {
                return ($row->received_by);
            })
            ->editColumn('product_number', function ($row) {
                return ($row->product_number);
            })
            ->editColumn('lead_time', function ($row) {
                if(!is_null($row->lead_time)) {
                    return Carbon::parse($row->lead_time)->format($this->global->date_format);
                }
                return '';
            })
            ->editColumn('est_ship_date', function ($row) {
                
                if(!is_null($row->est_ship_date)) {
                    return Carbon::parse($row->est_ship_date)->format($this->global->date_format);
                }
                return '';
            })
            ->editColumn('act_ship_date', function ($row) {
                if(!is_null($row->act_ship_date)) {
                    return Carbon::parse($row->act_ship_date)->format($this->global->date_format);
                }
                return '';
            })
            ->editColumn('est_receive_date', function ($row) {
                if(!is_null($row->est_receive_date)) {
                    return Carbon::parse($row->est_receive_date)->format($this->global->date_format);
                }
                return '';
            })
             ->editColumn('act_receive_date', function ($row) {
                if(!is_null($row->act_receive_date)) {
                    return Carbon::parse($row->act_receive_date)->format($this->global->date_format);
                }
                return '';
            })
            ->editColumn('est_install_date', function ($row) {
                if(!is_null($row->est_install_date)) {
                    return Carbon::parse($row->est_install_date)->format($this->global->date_format);
                }
                return '';
            })
             ->editColumn('act_Install_date', function ($row) {
                if(!is_null($row->act_Install_date)) {
                    return Carbon::parse($row->act_Install_date)->format($this->global->date_format);
                }
                return '';
            })
            ->editColumn('spec_number', function ($row) {
                return ($row->spec_number);
            })
             ->editColumn('quantity', function ($row) {
                return ($row->quantity);
            })
            
            
            
            
//            ->editColumn('clientDeposit', function ($row) {
//               $item = json_decode($row->item);
//               if(isset($item->clientDeposit)) {
//                   return ucfirst($item->clientDeposit);
//               }
//               return '';
//                
//            })
//            ->editColumn('totalEstimatedCost', function ($row) {
//               $item = json_decode($row->item);
//               if(isset($item->totalEstimatedCost)) {
//                   return ucfirst($item->totalEstimatedCost);
//               }
//               return '';
//                
//            })
//            ->editColumn('totalSalesPrice', function ($row) {
//               $item = json_decode($row->item);
//               if(isset($item->totalSalesPrice)) {
//                   return ucfirst($item->totalSalesPrice);
//               }
//               return '';
//                
//            })
//            ->editColumn('manufacturer', function ($row) {
//               $specification = json_decode($row->specification);
//               if(!empty($specification) && isset($specification->manufacturer)) {
//                return ucfirst($specification->manufacturer);
//               } 
//               return '';
//            })
            
            
            
           
            // ->editColumn('allow_purchase', function ($row) {
            //     if ($row->allow_purchase == 1) {
            //         return '<label class="label label-success">' . __('app.allowed') . '</label>';
            //     } else {
            //         return '<label class="label label-danger">' . __('app.notAllowed') . '</label>';
            //     }
            // })
            // ->editColumn('price', function ($row) {
            //     if (!is_null($row->taxes)) {
            //         $totalTax = 0;
            //         foreach (json_decode($row->taxes) as $tax) {
            //             $this->tax = Product::taxbyid($tax)->first();
            //             $totalTax = $totalTax + ($row->price * ($this->tax->rate_percent / 100));
            //         }
            //         return currency_position(($row->price + $totalTax),$this->global->currency->currency_symbol);
            //     }
            //    return currency_position($row->price,$this->global->currency->currency_symbol);

            // })
            ->addIndexColumn()
           ->rawColumns(['pPicture','action']);
            // ->rawColumns(['action', 'price', 'allow_purchase']);

    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Product $model)
    {
        $request = $this->request();
        $model = $model->select('products.*');    //, 'price', 'taxes', 'allow_purchase');
        
        if (!is_null($this->project_id)) {
            //$model->where('project_id', $this->project_id);
             $model->join('product_projects', 'product_projects.product_id', '=', 'products.id');
             $model->where('product_projects.project_id', $this->project_id);
        }
        // old method
//        if ($request->locationCode != 'all' && !is_null($request->locationCode)) {
//            $model = $model->where('item->locationCode', '=', $request->locationCode);
//        }
//        
        // new method
        if ($request->locationCode != 'all' && !is_null($request->locationCode)) {
            //$model->where('project_id', $this->project_id);
             $model->join('product_code_types', 'product_code_types.product_id', '=', 'products.id');
             $model->where('product_code_types.code_type_id', $request->locationCode);
        }
        
        
        if ($request->salesCategory != 'all' && !is_null($request->salesCategory)) {
           $model = $model->where('item->salesCategory', '=', $request->salesCategory);
        }
        $model = $model->orderBy('order_page', 'asc');
        //var_dump($request->salesCategory);exit;
        return $model;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('products-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-6'l><'col-md-6'Bf>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>")
            ->orderBy(0)
            ->destroy(true)
            ->responsive(true)
            ->serverSide(true)
            ->stateSave(true)
            ->processing(true)
            ->pageLength(25)
            //->scrollX(true)
            //->scrollY(200)
                
            //->setRowData(['data-id' => 'id'])
            ->language(__("app.datatable"))
            ->buttons(
                Button::make(['extend' => 'export', 'buttons' => ['excel', 'csv'], 'text' => '<i class="fa fa-download"></i> ' . trans('app.exportExcel') . '&nbsp;<span class="caret"></span>'])
            )
                //"lengthMenu" => [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
            ->parameters([
                "lengthMenu" => [[25, 50, 100], [25, 50, 100]],
                "paging" => true,
                'initComplete' => 'function () {
                   window.LaravelDataTables["products-table"].buttons().container()
                    .appendTo( ".bg-title .text-right");
                }',
                
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    });
                $("#products-table tbody tr").addClass("row-st");
                $("#products-table").sortable({ items: "tr", cursor: "move", opacity: 0.6, update: function() { updatePageOrder(); }});
                 
                 
                setTimeout(function(){  
                autoToggleFilter();
                }, 3000);
                 
                setTimeout(function(){  
                loadTabledit();
                }, 2000);
                
                }',
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'id' => ['data' => 'id', 'name' => 'id',  'searchable' => false], //0
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false ], //1
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->width(100)
                ->addClass('text-center'),
            'Spec_#' => ['data' => 'spec_number', 'name' => 'spec_number', 'orderable' => false], //2
            __('app.name') => ['data' => 'name', 'name' => 'name', 'orderable' => false], //3
            'picture' => ['data' => 'pPicture', 'name' => 'picture', 'orderable' => false], //4
            'projectName' => ['data' => 'projectName', 'name' => 'projectName', 'orderable' => false], //5
            'Location' => ['data' => 'locationCode', 'name' => 'locationCode' , 'orderable' => false], //6
            'Category' => ['data' => 'salesCategory', 'name' => 'salesCategory', 'orderable' => false], //7
            'Vendor' => ['data' => 'vendor', 'name' => 'vendor', 'orderable' => false], //8
            'Manufacturer' => ['data' => 'manufacturer', 'name' => 'manufacturer', 'orderable' => false], //9
            'Notes' => ['data' => 'notes', 'name' => 'notes', 'orderable' => false], //10
            'url' => ['data' => 'url', 'name' => 'url', 'orderable' => false], //11
            'Dimensions' => ['data' => 'dimensions', 'name' => 'dimensions', 'orderable' => false], //12
            'Materials' => ['data' => 'materials', 'name' => 'materials', 'orderable' => false], //13
            'QTY' => ['data' => 'quantity', 'name' => 'quantity', 'orderable' => false], //14
            'cost_per_unit' => ['data' => 'cost_per_unit', 'name' => 'cost_per_unit', 'orderable' => false], //15
            'markup_'.$this->global->currency->currency_symbol => ['data' => 'markup_fix', 'name' => 'markup_fix', 'orderable' => false], //16
            'markup_%' => ['data' => 'markup_per', 'name' => 'markup_per', 'orderable' => false], //17
            'Markup % Total' => ['data' => 'default_markup_fix', 'name' => 'default_markup_fix', 'orderable' => false], //18
            
            //'sales_tax'.$this->global->currency->currency_symbol => ['data' => 'sales_tax_fix', 'name' => 'sales_tax_fix', 'orderable' => false], //17
            //'sales_tax_%' => ['data' => 'sales_tax_per', 'name' => 'sales_tax_per', 'orderable' => false], //18
            
            'freight' => ['data' => 'freight', 'name' => 'freight', 'orderable' => false], //19
            'total_sale' => ['data' => 'total_sale', 'name' => 'total_sale', 'orderable' => false], //20
            'msrp' => ['data' => 'msrp', 'name' => 'msrp', 'orderable' => false], //21
            'acknowledgement' => ['data' => 'acknowledgement', 'name' => 'acknowledgement', 'orderable' => false], //22
            'est._ship_date' => ['data' => 'est_ship_date', 'name' => 'est_ship_date', 'orderable' => false], //23
            'act._ship_date' => ['data' => 'act_ship_date', 'name' => 'act_ship_date', 'orderable' => false], //24
            'est_receive_date' => ['data' => 'est_receive_date', 'name' => 'est_receive_date', 'orderable' => false], //25
            'act._receive_date' => ['data' => 'act_receive_date', 'name' => 'act_receive_date', 'orderable' => false], //26
            'received_by' => ['data' => 'received_by', 'name' => 'received_by', 'orderable' => false], //27
            'est._install_date' => ['data' => 'est_install_date', 'name' => 'est_install_date', 'orderable' => false], //28
            'act._Install_date' => ['data' => 'act_Install_date', 'name' => 'act_Install_date', 'orderable' => false], //29
            'product_number' => ['data' => 'product_number', 'name' => 'product_number', 'orderable' => false], //30
            'finish_color' => ['data' => 'finish_color', 'name' => 'finish_color', 'orderable' => false], //31
            
            
            'cost_per_unit' => ['exportable' => false, 'orderable' => false],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Products_' . date('YmdHis');
    }

    public function pdf()
    {
        set_time_limit(0);
        if ('snappy' == config('datatables-buttons.pdf_generator', 'snappy')) {
            return $this->snappyPdf();
        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('datatables::print', ['data' => $this->getDataForPrint()]);

        return $pdf->download($this->getFilename() . '.pdf');
    }
}
