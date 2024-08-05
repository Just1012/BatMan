<?php

namespace App\Services;

use Exception;
use App\Models\Service;
use App\Models\ServiceField;
use Brian2694\Toastr\Facades\Toastr;

class ServiceService
{
    public function getService($category)
    {
        $data = Service::where('category_id', $category->id)->get();
        foreach ($data as $value) {
            $value->multiImages = json_decode($value->multiImages);
            $value['total'] = number_format($value->price - $value->discount, 2);
        }
        return  $data;
    }


    public function storeService($request)
    {
        try {
            if($request->price > $request->discount ){

        $requestData = $request->except('select');
        // Check if there is an old image
        $oldService = Service::find($requestData['id'] ?? null);
        $oldImage = $oldService ? $oldService->image : null;
        $oldMultiImages = $oldService ? json_decode($oldService->multiImages) : [];

        // Handle the new single image upload
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('images'), $imageName);
            $requestData['image'] = $imageName;

            // Delete the old image file if it exists
            if ($oldImage && file_exists(public_path('images/' . $oldImage))) {
                unlink(public_path('images/' . $oldImage));
            }
        }

        // Handle the new multi images upload
        if ($request->hasFile('multiImages')) {
            $data = [];
            foreach ($request->file('multiImages') as $key => $file) {
                $imageName = time() . '_' . $key . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images'), $imageName);
                $data[] = $imageName;
            }

            // Merge new multi images with old multi images and delete old images
            $requestData['multiImages'] = json_encode(array_merge($oldMultiImages, $data));
            foreach ($oldMultiImages as $oldImage) {
                if (file_exists(public_path('images/' . $oldImage))) {
                    unlink(public_path('images/' . $oldImage));
                }
            }
        }

        $service = Service::updateOrCreate(
            ['id' => $requestData['id'] ?? null],
            $requestData
        );
        
        
        
       
        $selectedFields = $request->input('select', []);

// Delete existing ServiceField records for the service that are not in the selectedFields array
        ServiceField::where('services_id', $service->id)
            ->whereNotIn('field_id', $selectedFields)
            ->delete();
   
            
        
        // Iterate through the selected fields
        foreach ($selectedFields as $fieldId) {
            // Check if a ServiceField record already exists for the current service and field ID
            $serviceField = ServiceField::firstOrNew([
                'services_id' => $service->id,
                'field_id' => $fieldId,
            ]);
        
            // If the ServiceField record doesn't exist, save it
            if (!$serviceField->exists) {
                $serviceField->save();
            }
        }
        
                $successMessage = $requestData['id'] ? 'تم تعديل الخدمة بنجاح' : 'تم إضافة الخدمة بنجاح';
                Toastr::success($successMessage, 'تم بنجاح');

        return $successMessage;
            }else{
                $successMessage="لا يمكن الخصم اكبر من السعر";
                Toastr::info($successMessage,  'تنبيه');

                  return $successMessage;
            }

    } catch (\Throwable $th) {
        Toastr::error('أعد المحاولة', 'خطاء');
        return 'أعد المحاولة';
    }

    }


    
    
    
    
    
    
    
    
    public function updateStatus($service)
    {
        try {
            $service->update([
                'status' => $service->status == 0 ? 1 : 0
            ]);


            $successMessage = $service->status == 1 ?
                'تم تفعيل الخدمة بنجاح' :
                'تم إلغاء تفعيل الخدمة بنجاح';

            return $successMessage;
        } catch (\Throwable $th) {
            return response()->json(['status' => '404']);
        }
    }
}
