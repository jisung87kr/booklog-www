<x-app-layout>
    @section('title', '개인정보처리방침 - ' . config('app.name'))
    @section('description', '북로그 서비스의 개인정보 수집 및 이용에 대한 안내입니다.')

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8">
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 mb-4">개인정보처리방침</h1>
                        <p class="text-gray-600">북로그는 개인정보보호법에 따라 이용자의 개인정보 보호 및 권익을 보호하고 개인정보와 관련한 이용자의 고충을 원활하게 처리할 수 있도록 다음과 같은 처리방침을 두고 있습니다.</p>
                    </div>

                    <div class="space-y-8">
                        <!-- 1. 개인정보의 처리목적 -->
                        <section>
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">1. 개인정보의 처리목적</h2>
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <p class="text-gray-700 mb-4">북로그는 다음의 목적을 위하여 개인정보를 처리합니다. 처리하고 있는 개인정보는 다음의 목적 이외의 용도로는 이용되지 않으며, 이용목적이 변경되는 경우에는 개인정보보호법 제18조에 따라 별도의 동의를 받는 등 필요한 조치를 이행할 예정입니다.</p>
                                
                                <h3 class="text-lg font-medium text-gray-800 mb-3">가. 홈페이지 회원가입 및 관리</h3>
                                <ul class="list-disc pl-6 mb-4 space-y-1">
                                    <li class="text-gray-700">회원 가입의사 확인, 회원제 서비스 제공에 따른 본인 식별·인증</li>
                                    <li class="text-gray-700">회원자격 유지·관리, 서비스 부정이용 방지</li>
                                    <li class="text-gray-700">각종 고지·통지, 고충처리 등을 목적으로 개인정보를 처리합니다.</li>
                                </ul>

                                <h3 class="text-lg font-medium text-gray-800 mb-3">나. 민원사무 처리</h3>
                                <ul class="list-disc pl-6 space-y-1">
                                    <li class="text-gray-700">민원인의 신원 확인, 민원사항 확인, 사실조사를 위한 연락·통지</li>
                                    <li class="text-gray-700">처리결과 통보 등의 목적으로 개인정보를 처리합니다.</li>
                                </ul>
                            </div>
                        </section>

                        <!-- 2. 개인정보의 처리 및 보유기간 -->
                        <section>
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">2. 개인정보의 처리 및 보유기간</h2>
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <div class="overflow-x-auto">
                                    <table class="w-full border-collapse border border-gray-300">
                                        <thead>
                                            <tr class="bg-gray-100">
                                                <th class="border border-gray-300 px-4 py-3 text-left">처리목적</th>
                                                <th class="border border-gray-300 px-4 py-3 text-left">개인정보 항목</th>
                                                <th class="border border-gray-300 px-4 py-3 text-left">보유기간</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="border border-gray-300 px-4 py-3">회원가입 및 관리</td>
                                                <td class="border border-gray-300 px-4 py-3">이름, 이메일, 사용자명</td>
                                                <td class="border border-gray-300 px-4 py-3">회원탈퇴시까지</td>
                                            </tr>
                                            <tr>
                                                <td class="border border-gray-300 px-4 py-3">민원사무 처리</td>
                                                <td class="border border-gray-300 px-4 py-3">이름, 이메일, 문의내용</td>
                                                <td class="border border-gray-300 px-4 py-3">처리완료 후 3년</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>

                        <!-- 3. 개인정보의 제3자 제공 -->
                        <section>
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">3. 개인정보의 제3자 제공</h2>
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <p class="text-gray-700">북로그는 원칙적으로 이용자의 개인정보를 외부에 제공하지 않습니다. 다만, 아래의 경우에는 예외로 합니다.</p>
                                <ul class="list-disc pl-6 mt-4 space-y-2">
                                    <li class="text-gray-700">이용자가 사전에 동의한 경우</li>
                                    <li class="text-gray-700">법령의 규정에 의거하거나, 수사 목적으로 법령에 정해진 절차와 방법에 따라 수사기관의 요구가 있는 경우</li>
                                </ul>
                            </div>
                        </section>

                        <!-- 4. 개인정보처리의 위탁 -->
                        <section>
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">4. 개인정보처리의 위탁</h2>
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <p class="text-gray-700 mb-4">북로그는 원활한 개인정보 업무처리를 위하여 다음과 같이 개인정보 처리업무를 위탁하고 있습니다.</p>
                                
                                <div class="overflow-x-auto">
                                    <table class="w-full border-collapse border border-gray-300">
                                        <thead>
                                            <tr class="bg-gray-100">
                                                <th class="border border-gray-300 px-4 py-3 text-left">위탁업체</th>
                                                <th class="border border-gray-300 px-4 py-3 text-left">위탁업무</th>
                                                <th class="border border-gray-300 px-4 py-3 text-left">보유기간</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="border border-gray-300 px-4 py-3">AWS</td>
                                                <td class="border border-gray-300 px-4 py-3">클라우드 서비스 제공</td>
                                                <td class="border border-gray-300 px-4 py-3">위탁계약 종료시까지</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>

                        <!-- 5. 정보주체의 권리·의무 -->
                        <section>
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">5. 정보주체의 권리·의무 및 행사방법</h2>
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <p class="text-gray-700 mb-4">이용자는 개인정보주체로서 다음과 같은 권리를 행사할 수 있습니다.</p>
                                <ul class="list-disc pl-6 space-y-2">
                                    <li class="text-gray-700">개인정보 처리현황 통지요구</li>
                                    <li class="text-gray-700">개인정보 처리정지 요구</li>
                                    <li class="text-gray-700">개인정보의 정정·삭제 요구</li>
                                    <li class="text-gray-700">손해배상 청구</li>
                                </ul>
                            </div>
                        </section>

                        <!-- 6. 개인정보의 파기 -->
                        <section>
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">6. 개인정보의 파기</h2>
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <p class="text-gray-700 mb-4">북로그는 개인정보 보유기간의 경과, 처리목적 달성 등 개인정보가 불필요하게 되었을 때에는 지체없이 해당 개인정보를 파기합니다.</p>
                                
                                <h3 class="text-lg font-medium text-gray-800 mb-3">파기절차</h3>
                                <ul class="list-disc pl-6 mb-4 space-y-1">
                                    <li class="text-gray-700">이용자가 입력한 정보는 목적 달성 후 별도의 DB에 옮겨져 내부 방침 및 기타 관련 법령에 따라 일정기간 저장된 후 혹은 즉시 파기됩니다.</li>
                                    <li class="text-gray-700">이 때, DB로 옮겨진 개인정보는 법률에 의한 경우가 아니고서는 다른 목적으로 이용되지 않습니다.</li>
                                </ul>

                                <h3 class="text-lg font-medium text-gray-800 mb-3">파기방법</h3>
                                <ul class="list-disc pl-6 space-y-1">
                                    <li class="text-gray-700">전자적 파일 형태의 정보는 기록을 재생할 수 없는 기술적 방법을 사용합니다.</li>
                                    <li class="text-gray-700">종이에 출력된 개인정보는 분쇄기로 분쇄하거나 소각을 통하여 파기합니다.</li>
                                </ul>
                            </div>
                        </section>

                        <!-- 7. 개인정보 보호책임자 -->
                        <section>
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">7. 개인정보 보호책임자</h2>
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <p class="text-gray-700 mb-4">북로그는 개인정보 처리에 관한 업무를 총괄해서 책임지고, 개인정보 처리와 관련한 정보주체의 불만처리 및 피해구제 등을 위하여 아래와 같이 개인정보 보호책임자를 지정하고 있습니다.</p>
                                
                                <div class="bg-white p-4 border border-gray-200 rounded-lg">
                                    <h3 class="text-lg font-medium text-gray-800 mb-3">▶ 개인정보 보호책임자</h3>
                                    <ul class="space-y-1">
                                        <li class="text-gray-700"><strong>성명:</strong> 북로그 관리자</li>
                                        <li class="text-gray-700"><strong>연락처:</strong> admin@booklog.com</li>
                                        <li class="text-gray-700"><strong>처리부서:</strong> 운영팀</li>
                                    </ul>
                                </div>
                                
                                <p class="text-gray-700 mt-4">※ 개인정보 보호담당부서로 연결됩니다.</p>
                            </div>
                        </section>

                        <!-- 8. 개인정보 처리방침 변경 -->
                        <section>
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">8. 개인정보 처리방침 변경</h2>
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <p class="text-gray-700 mb-4">이 개인정보처리방침은 시행일로부터 적용되며, 법령 및 방침에 따른 변경내용의 추가, 삭제 및 정정이 있는 경우에는 변경사항의 시행 7일 전부터 공지사항을 통하여 고지할 것입니다.</p>
                                
                                <div class="bg-blue-50 p-4 border border-blue-200 rounded-lg">
                                    <p class="text-blue-800"><strong>본 방침은 2025년 9월 3일부터 시행됩니다.</strong></p>
                                </div>
                            </div>
                        </section>

                        <!-- 문의 관련 특별 조항 -->
                        <section class="border-t-2 border-blue-200 pt-8">
                            <h2 class="text-xl font-semibold text-blue-900 mb-4">📧 문의하기 서비스 관련 개인정보 수집·이용</h2>
                            <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                                <h3 class="text-lg font-medium text-blue-800 mb-3">수집하는 개인정보 항목</h3>
                                <ul class="list-disc pl-6 mb-4 space-y-1">
                                    <li class="text-blue-700"><strong>필수항목:</strong> 이름, 이메일 주소, 문의 제목, 문의 내용</li>
                                    <li class="text-blue-700"><strong>선택항목:</strong> 문의 유형</li>
                                </ul>

                                <h3 class="text-lg font-medium text-blue-800 mb-3">수집 및 이용목적</h3>
                                <ul class="list-disc pl-6 mb-4 space-y-1">
                                    <li class="text-blue-700">문의사항 접수 및 처리</li>
                                    <li class="text-blue-700">문의에 대한 답변 및 결과 통보</li>
                                    <li class="text-blue-700">서비스 개선을 위한 통계 분석 (개인식별 불가 형태)</li>
                                </ul>

                                <h3 class="text-lg font-medium text-blue-800 mb-3">보유 및 이용기간</h3>
                                <p class="text-blue-700 mb-4">문의 처리 완료 후 <strong>3년간</strong> 보관 후 자동 파기됩니다.</p>

                                <div class="bg-white p-4 border border-blue-300 rounded-lg">
                                    <p class="text-blue-800"><strong>⚠️ 동의 거부권 및 불이익</strong></p>
                                    <p class="text-blue-700 text-sm mt-2">위의 개인정보 수집·이용에 대한 동의를 거부할 권리가 있습니다. 그러나 동의를 거부할 경우 문의하기 서비스 이용이 제한될 수 있습니다.</p>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="mt-12 pt-8 border-t border-gray-200 text-center">
                        <button onclick="window.close()" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 mr-4">
                            <i class="fas fa-times mr-2"></i>창 닫기
                        </button>
                        <a href="{{ route('contact.create') }}" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                            <i class="fas fa-arrow-left mr-2"></i>문의하기로 돌아가기
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>